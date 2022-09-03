<?php

namespace App\Repository;

use App\Entity\Vente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Vente|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vente|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vente[]    findAll()
 * @method Vente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VenteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vente::class);
    }

    // /**
    //  * @return Vente[] Returns an array of Vente objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Vente
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getVentes()
    {
        $qb = $this->createQueryBuilder('v')
            ->select('v')
            ->orderBy('v.id', 'DESC')
            ->getQuery()
            ->getResult();
        return $qb;
    }

    public function getRapportDateInterval($dateFrom, $dateTo, $structure)
    {
        $qb = $this->createQueryBuilder('v')
            ->select('a.NomApprenant, a.PrenomApprenant, n.TitreNiveau, n.Slug, t.montant, t.dateDebut , t.dateFin')
            ->innerJoin('v.ticket', 't')
            ->innerJoin('t.apprenant', 'a')
            ->innerJoin('a.niveau', 'n')
            ->innerJoin('v.user', 'u')
            ->innerJoin('u.structure', 's')
            ->where('s.id = :structureId and t.statut = :statut')
            ->setParameter('structureId', $structure)
            ->setParameter('statut', 1)
            ->groupBy('t.dateFin');
        if ($dateFrom) {
            $qb->andwhere('t.dateFin >= :dateFrom ');
            $qb->setParameter('dateFrom', $dateFrom);
        }
        if ($dateTo) {
            $qb->andwhere('t.dateFin <= :dateTo ');
            $qb->setParameter('dateTo', $dateTo);
        }
        return $qb
            ->getQuery()
            ->getResult();
    }

    // public function getRapport($structure, $dateFrom, $dateTo)
    // {
    //     $qb = $this->createQueryBuilder('v')
    //         ->select('a.NomApprenant, a.PrenomApprenant, n.TitreNiveau, n.Slug, t.montant, t.dateFin')
    //         ->innerJoin('v.ticket', 't')
    //         ->innerJoin('t.apprenant', 'a')
    //         ->innerJoin('a.niveau', 'n')
    //         // ->innerJoin('v.momentCategorie', 'c')
    //         // ->innerJoin('v.plat', 'p')
    //         ->innerJoin('v.user', 'u')
    //         ->innerJoin('u.structure', 's')
    //         ->where('s.id = :structureId and t.statut = :statut')
    //         ->setParameter('structureId', $structure)
    //         // ->where('v.statut = :statut')
    //         ->setParameter('statut', 1)
    //         ->groupBy('t.dateDebut');
    //     if ($dateFrom) {
    //         $qb->andwhere('t.dateFin >= :dateFrom ');
    //         $qb->setParameter('dateFrom', $dateFrom);
    //     }
    //     if ($dateTo) {
    //         $qb->andwhere('t.dateFin <= :dateTo ');
    //         $qb->setParameter('dateTo', $dateTo);
    //     }
    //     return $qb
    //         ->getQuery()
    //         ->getResult();
    // }

    public function getGrapheVente($structure)
    {
        $qb = $this->createQueryBuilder('v')
            ->select('count(v.id) as nombreGrapheVente, sum(t.montant) as montantGrapheVente, t.dateDebut as dateGrapheVente')
            ->innerJoin('v.ticket', 't')
            ->innerJoin('v.user', 'u')
            ->innerJoin('u.structure', 's')
            ->where('s.id = :structureId')
            ->setParameter('structureId', $structure)
            ->groupBy('t.dateDebut');
        return
            $qb->getQuery()->getResult();
        return $qb;
    }
}
