<?php

namespace App\Controller;

use App\Entity\Session;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{   
    #[Route('/', name: 'app_home')]
    #[Route('/home', name: 'app_home')]
    public function index(SessionRepository $sr ,Security $security, EntityManagerInterface $em): Response
    {
       
        $dateActuelle = new \DateTime();

        $fini = $em
            ->getRepository(Session::class)
            ->createQueryBuilder('s')
            ->where('s.fin < :date')
            ->setParameter('date', $dateActuelle)
            ->getQuery()
            ->getResult();

        $enCour = $em
            ->getRepository(Session::class)
            ->createQueryBuilder('s')
            ->where(':date BETWEEN s.debut AND s.fin')
            ->setParameter('date', $dateActuelle)
            ->getQuery()
            ->getResult();

        $aVenir = $em
            ->getRepository(Session::class)
            ->createQueryBuilder('s')
            ->where('s.debut > :date')
            ->setParameter('date', $dateActuelle)
            ->getQuery()
            ->getResult();

        
        return $this->render('home/index.html.twig', [
            'fini' => $fini,
            'enCour'=>$enCour,
            'aVenir'=>$aVenir
        ]);
    
    }
}
