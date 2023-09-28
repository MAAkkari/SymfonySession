<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormationController extends AbstractController
{
    #[Route('/formation', name: 'app_formation')]
    public function index(FormationRepository $fr): Response
    {

        return $this->render('formation/index.html.twig', [
            'formations'=>$fr->findAll()
        ]);
    }

    #[Route('/formation/new', name: 'new_formation')]
    #[Route('/formation/{id}/edit', name: 'edit_formation')] // si on veux rajouter l'edition 
    public function new_edit(Formation $formation =null ,Request $request , EntityManagerInterface $entityManager):Response {

	if(!$formation ){$formation = new Formation();} //////////// crée une nouvelle formation ( un objet pas dans la bdd ) } // si on veux rajouter l'edition sinon on enleve le if mais on garde l'interrieur
        
        $form = $this->createForm(FormationType::class, $formation); ////// ( crée le formulaire )

 	$form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() ){ // si le form est submit et qu'il est valide
            $formation = $form->getData(); //on met les info dans l'entité formation crée plus haut  
            $entityManager->persist($formation); // prepare en pdo 
            $entityManager->flush(); // execute en pdo
            $this->addFlash("success","creation/modification de la formation avec succes");
            return $this->redirectToRoute('app_formation');
        }


        return $this->render('formation/new.html.twig',[  //////// ( envoie du form dans la vue ) 
            'FormAddFormation'=> $form  ,
        ]);
    }
    #[Route('/formation/{id}/delete', name: 'delete_formation')]
    public function delete(Formation $formation , EntityManagerInterface $em){   
        $em->remove($formation);
        $em->flush();
        $this->addFlash("success","suppression de la formation avec succes");
        return $this->redirectToRoute('app_formation');
    }


    #[Route('/formation/{id}', name: 'show_formation')]
    public function show(Formation $formation , FormationRepository $fr): Response
    {    
        return $this->render('formation/show.html.twig', [
            'formation'=>$fr->find($formation->getId()),
        ]);
    }
}
