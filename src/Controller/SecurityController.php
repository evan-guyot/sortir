<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Site;
use App\Form\RegisterType;
use App\Repository\ImageRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\UnavailableStream;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, ParticipantRepository $participantRepository): Response
    {
        $user = new Participant();


        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            if($participantRepository->hasSamePseudo($user->getPseudo()) ){

                $this->addFlash("error", "Ce pseudo est déjà utilisé");

                return $this->redirectToRoute("app_register");
            }

            if($participantRepository->hasSameMail($user->getMail()) ){

                $this->addFlash("error", "Cette adresse mail est déjà utilisée");

                return $this->redirectToRoute("app_register");
            }

            $user->setRoles(["ROLE_USER"]);

            if($user->isAdministrateur()){
                $user->setRoles(["ROLE_ADMIN"]);
            }

            if($user->isActif()){
                $user->setRoles(["ROLE_ACTIVE"]);
            }

            $user->setMotdepasse(
                $userPasswordHasher->hashPassword(
                    $user,
                    $user->getMotdepasse()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success", "Le compte vient d'être créé");

            return $this->redirectToRoute("app_register");
        }

        return $this->render('security/register.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * @throws UnavailableStream
     * @throws Exception
     */
    #[Route(path: '/registerFile', name: 'app_register_file')]
    public function registerFile(Request $request, SiteRepository $siteRepository, ImageRepository $imageRepository, EntityManagerInterface $entityManager): Response
    {
        // Créez un formulaire de téléchargement de fichier CSV
        $form = $this->createFormBuilder()
            ->add('csvFile', FileType::class, ['label' => 'Sélectionner le fichier CSV'])
            ->add('save', SubmitType::class, ['label' => 'Importer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $csvFile = $form['csvFile']->getData();

            if ($csvFile) {

                if ($csvFile->getClientOriginalExtension() !== 'csv') {
                    $errorMessage = 'Le fichier doit être un fichier CSV.';
                    return $this->render('security/register_file.html.twig', [
                        'form' => $form->createView(),
                        'error_message' => $errorMessage,
                    ]);
                }

                $csvFilePath = $csvFile->getRealPath();
                $csvReader = Reader::createFromPath($csvFilePath);
                $csvReader->setHeaderOffset(0); // Si la première ligne contient les en-têtes

                foreach ($csvReader as $record) {
                    $recordString = key($record); // Récupère la première (et unique) clé du tableau
                    $dataString = current($record); // Récupère la première (et unique) valeur du tableau

                    $headers = explode(';', $recordString);
                    $dataValues = explode(';', $dataString);
                    $dataAssociative = array_combine($headers, $dataValues);

//                    dd($dataAssociative);

                    $siteId = $dataAssociative['site_id'];
                    $pseudo = $dataAssociative['pseudo'];
                    $roles = array('ROLE_USER');
                    $nom = $dataAssociative['nom'];
                    $prenom = $dataAssociative['prenom'];
                    $telephone = $dataAssociative['telephone'];
                    $mail = $dataAssociative['mail'];
                    $motdepasse = $dataAssociative['motdepasse'];
                    $administrateur = $dataAssociative['administrateur'];
                    $actif = $dataAssociative['actif'];
                    $imageId = $dataAssociative['image_id'];

                    // Création d'un nouvel objet VotreEntity et définition des valeurs
                    $user = new Participant();
                    $site = $siteRepository->findOneBy(['id' => $siteId]);
                    $image = $imageRepository->findOneBy(['id' => $imageId]);

                    if ($actif) {
                        $roles[] = 'ROLE_ACTIF';
                    } else {
                        $roles[] = 'ROLE_INACTIF';
                    }

                    if ($administrateur) {
                        $roles[] = 'ROLE_ADMIN';
                    }

                    $user->setSite($site);
                    $user->setPseudo($pseudo);
                    $user->setRoles($roles);
                    $user->setNom($nom);
                    $user->setPrenom($prenom);
                    $user->setTelephone($telephone);
                    $user->setMail($mail);
                    $user->setMotdepasse($motdepasse);
                    $user->setAdministrateur($administrateur);
                    $user->setActif($actif);
                    $user->setImage($image);

//                    dd($user);
                    $entityManager->persist($user);
                    $entityManager->flush();
                }

                $successMessage = 'Utilisateurs importés avec succès!';
                return $this->render('security/register_file.html.twig', [
                    'form' => $form->createView(),
                    'success_message' => $successMessage,
                ]);
            }
        }

        return $this->render('security/register_file.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
