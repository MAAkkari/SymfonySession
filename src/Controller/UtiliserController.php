<?php

namespace App\Controller;

use App\Entity\Utiliser;
use App\Form\UtiliserType;
use App\Repository\UtiliserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UtiliserController extends AbstractController
{
    #[Route('/utiliser', name: 'app_utiliser')]
    public function index(UtiliserRepository $ur): Response
    {
        return $this->render('utiliser/index.html.twig', [
            'utilisers' => $ur->findAll()
        ]);
    }
    #[Route('/utiliser/{id}/augmenter', name: 'augmenter_utiliser')]
    public function augmenter(Utiliser $utiliser,UtiliserRepository $ur , EntityManagerInterface $EntityManager){
        $newqtt=$utiliser->getQtt()+1;
        if($newqtt > $utiliser->getMateriel()->getQtt()){
            $this->addFlash("error","la Quantité de  ''".$utiliser->getMateriel()->getNom()."'' est insuffisante");
        }else{
            $utiliser->setQtt($newqtt);
            $EntityManager->persist($utiliser);
            $EntityManager->flush();
            $this->addFlash("success","ajout d'un ''".$utiliser->getMateriel()->getNom()."'' reussi ");
        }
        return $this->redirectToRoute('show_session', ['id' => $utiliser->getSession()->getId()]);
    }

    #[Route('/utiliser/{id}/diminuer', name: 'diminuer_utiliser')]
    public function diminuer(Utiliser $utiliser,UtiliserRepository $ur , EntityManagerInterface $EntityManager){
        $newqtt=$utiliser->getQtt()-1;
        $idSession=$utiliser->getSession()->getId();
        if ( $newqtt <= 0) { 
            $EntityManager->remove($utiliser); 
            $this->addFlash("success","suppression de ''".$utiliser->getMateriel()->getNom()."'' reussi ");
        } else{
            $utiliser->setQtt($newqtt);
            $EntityManager->persist($utiliser);
            $this->addFlash("success","diminution de ''".$utiliser->getMateriel()->getNom()."'' reussi ");
        }
        $EntityManager->flush();
        return $this->redirectToRoute('show_session', ['id' =>$idSession ]);
    }

    #[Route('/utiliser/{id}/remove', name: 'remove_utiliser')]
    public function remove(Utiliser $utiliser,UtiliserRepository $ur , EntityManagerInterface $EntityManager){
        $idSession=$utiliser->getSession()->getId();
        $EntityManager->remove($utiliser);
        $EntityManager->flush();
        $this->addFlash("success","suppression du materiel de cette session avec succés");
        return $this->redirectToRoute('show_session', ['id' =>$idSession ]);
    }

    
}
