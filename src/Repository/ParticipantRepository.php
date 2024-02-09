<?php

namespace App\Repository;

use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Participant>
 *
 * @method Participant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participant[]    findAll()
 * @method Participant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participant::class);
    }

    public function hasSamePseudo(string $pseudo)
    {
        return $this->createQueryBuilder('p')
                ->andWhere('p.pseudo = :pseudo')
                ->setParameter('pseudo', $pseudo)
                ->getQuery()
                ->getOneOrNullResult() != null;
    }

    public function hasSameMail(string $mail)
    {
        return $this->createQueryBuilder('p')
                ->andWhere('p.mail = :mail')
                ->setParameter('mail', $mail)
                ->getQuery()
                ->getOneOrNullResult() != null;
    }

    public function disable(int $id)
    {
        $user = $this->find($id);
        if (!$user) {
            throw new \Exception("Participant with id $id not found.");
        }
        $newRoles = array('ROLE_INACTIVE');
        foreach ($user->getRoles() as $role) {
            if ($role != 'ROLE_ACTIVE') {
                $newRoles[] = $role;
            }
        }
        $user->setRoles($newRoles);
        $user->setActif(false);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function enable(int $id)
    {
        $user = $this->find($id);
        if (!$user) {
            throw new \Exception("Participant with id $id not found.");
        }
        $newRoles = array('ROLE_ACTIVE');
        foreach ($user->getRoles() as $role) {
            if ($role != 'ROLE_INACTIVE') {
                $newRoles[] = $role;
            }
        }
        $user->setRoles($newRoles);
        $user->setActif(true);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function delete(int $id)
    {
        $user = $this->find($id);
        if (!$user) {
            throw new \Exception("Participant with id $id not found.");
        }
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
    }
//    /**
//     * @return Participant[] Returns an array of Participant objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Participant
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
