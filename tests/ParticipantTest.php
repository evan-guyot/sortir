<?php

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ParticipantTest extends KernelTestCase
{
    public function testAjoutParticipant() : void{

        self::bootKernel();

        $entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $site = new Site();
        $site->setNom("Nantes");

        $entityManager->persist($site);
        $entityManager->flush();

        $participant = new Participant();
        $participant->setPseudo('PseudoParticipant');
        $participant->setNom('NomParticipant');
        $participant->setPrenom('PrenomParticipant');
        $participant->setMail('participant@test.com');
        $participant->setActif(true);
        $participant->setAdministrateur(true);
        $participant->setMotdepasse("fakePassword");
        $participant->setSite($site);

        $entityManager->persist($participant);
        $entityManager->flush();

        $insertedProduct = $entityManager->getRepository(Participant::class)->findOneBy([
            'pseudo' => 'PseudoParticipant',
        ]);

        self::assertNotNull($insertedProduct);
        self::assertEquals('WrongParticipant', $insertedProduct->getNom());
        self::assertEquals('PrenomParticipant', $insertedProduct->getPrenom());
    }
}