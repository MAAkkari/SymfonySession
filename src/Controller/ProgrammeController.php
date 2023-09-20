<?php

namespace App\Controller;

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


    #[Route('/programme/new', name: 'new_programme')]
    #[Route('/programme/{id}/edit', name: 'edit_programme')] // rajouter l'edition 
    public function new_edit(Programme $programme =null ,Request $request , EntityManagerInterface $entityManager):Response {

	if(!$programme ){$programme = new programme(); // crée un nouveau programme ( un objet pas dans la bdd ) } // si on veux rajouter l'edition sinon on enleve le if mais on garde l'interrieur
        
        $form = $this->createForm(ProgrammeType::class, $programme); // ( crée le formulaire )

 	$form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() ){ // si le form est submit et qu'il est valide
            $programme = $form->getData(); //on met les info dans l'entité programme crée plus haut
            
            
           
            foreach($programme->getModule() as $module) {
        
                $entityManager->persist($session);
               
            }
            $entityManager->persist($programme); // prepare en pdo
            $entityManager->flush(); // execute en pdo
            
            return $this->redirectToRoute('app_programme');
        }


        return $this->render('programme/new.html.twig',[  //////// ( envoie du form dans la vue ) 
            'formAddprogramme'=> $form  ,
        ]);
    } }
}