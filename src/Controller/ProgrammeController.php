<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Programme;
use App\Form\ProgrammeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProgrammeController extends AbstractController
{
    #[Route('/programme', name: 'app_programme')]
    public function index(): Response
    {
        return $this->render('programme/index.html.twig', [
            'controller_name' => 'ProgrammeController',
        ]);
    }


    #[Route('/programme/{id}/new', name: 'new_programme')]
    public function new(Session $session , Request $request , EntityManagerInterface $entityManager):Response {

	$programme = new programme();
    $form = $this->createForm(ProgrammeType::class, $programme); // ( crée le formulaire )
    $id=$session->getId();
 	$form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() ){ // si le form est submit et qu'il est valide
            $programme = $form->getData(); //on met les info dans l'entité programme crée plus haut
            $programme->setSession($session);
            $entityManager->persist($programme); // prepare en pdo
            $entityManager->flush(); // execute en pdo
            return $this->redirectToRoute('show_session', ['id' => $id]);
        }


        return $this->render('programme/new.html.twig',[  //////// ( envoie du form dans la vue ) 
            'formAddProgramme'=> $form  ,
        ]);
    } 

    #[Route('/programme/{id}/delete', name: 'delete_programme')]
    public function delete(Programme $programme , EntityManagerInterface $em){
        $id=$programme->getSession()->getId();   
        $em->remove($programme);
        $em->flush();
        return $this->redirectToRoute('show_session', ['id' => $id]);
    }

}
    
