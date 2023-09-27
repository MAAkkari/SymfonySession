<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function findNonInscrits($session_id){
        $em = $this->getEntityManager();
        $sub = $em->createQueryBuilder();

        $qd = $sub;
        $qd->select('s')
            ->from('App\Entity\Stagiaire', 's')
            ->leftJoin('s.sessions','se')
            ->where('se.id =:id');

        $sub = $em->createQueryBuilder();
        //selectionner tout les stagiaires qui ne sont pas dans le resultat precedent donc tout ceux non inscrit a cette session
        $sub->select('st')
        ->from('App\Entity\Stagiaire','st')
        ->where($sub->expr()->notIn('st.id', $qd->getDQL()))
        ->setParameter('id', $session_id)
        ->orderBy('st.nom');

        $query=$sub->getQuery();
        return $query->getResult();

    }

    public function findNonProgrammes($session_id) {
        $em = $this->getEntityManager();
    
        // Subquery to select all "Programme" entities associated with the given session_id
        $qd = $em->createQueryBuilder();
        $qd->select('m')
            ->from('App\Entity\Module', 'm')
            ->leftJoin('m.programmes', 'pr')
            ->where('pr.session = :id')
            ->setParameter('id', $session_id);
    
        // Main query to select all "Programme" entities that are not in the subquery result
        $sub = $em->createQueryBuilder();
        $sub->select('mod')
            ->from('App\Entity\Module', 'mod')
            ->where($sub->expr()->notIn('mod.id', $qd->getDQL()))
            ->setParameter('id', $session_id);
        $query = $sub->getQuery();
        return $query->getResult();
    }
    
//    /**
//     * @return Session[] Returns an array of Session objects
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

//    public function findOneBySomeField($value): ?Session
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
