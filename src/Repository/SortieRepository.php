<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findWithFilters(Participant $user, bool $cb1, bool $cb2, bool $cb3, bool $cb4, string $site, string $motclef, $datedebut, $datefin): array
    {
        $today = new \DateTime();
        $datearchivage = $today->sub(new \DateInterval('P1M'));

        $qb = $this->createQueryBuilder('s')
            ->orderBy('s.id', 'ASC');

        $qb->andWhere('s.datedebut >= :archivage')
            ->setParameter('archivage', $datearchivage);

        if ($cb1) {
            $qb->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur', $user);
        }

        if ($cb2) {
            $qb->andWhere('EXISTS (
        SELECT i
        FROM App\Entity\Inscription i
        WHERE i.sortie = s AND i.participant = :user
    )')
                ->setParameter('user', $user);
        }

        if ($cb3) {
            $qb->andWhere('NOT EXISTS (
        SELECT i2
        FROM App\Entity\Inscription i2
        WHERE i2.sortie = s AND i2.participant = :user2
    )')
                ->setParameter('user2', $user);
        }

        if ($cb4) {
            $qb->andWhere('s.datedebut < :today')
                ->setParameter('today', $today);
        }


        if ($site != 'All') {

            $qb->andWhere('s.organisateur IN (
        SELECT p.id
        FROM App\Entity\Participant p
        JOIN p.site si
        WHERE si.nom = :siteNom
    )')
                ->setParameter('siteNom', $site);
        }

        if ($motclef) {
            $qb->andWhere('s.nom LIKE  :motclef')
                ->setParameter('motclef', '%' . $motclef . '%');
        }

        if($datedebut){
            $qb->andWhere('s.datedebut >= :datedebut')
                ->setParameter('datedebut', $datedebut);
        }
        
        if ($datefin) {
            $qb->andWhere('s.datedebut <= :datefin')
                ->setParameter('datefin', $datefin);
        }

        return $qb->getQuery()->getResult();

    }


//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
