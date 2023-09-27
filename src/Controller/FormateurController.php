<?php

namespace App\Controller;

use App\Entity\Formateur;
use App\Form\FormateurType;
use App\Repository\FormateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormateurController extends AbstractController
{
    #[Route('/formateur', name: 'app_formateur')]
    public function index(FormateurRepository $fr): Response
    {
        return $this->render('formateur/index.html.twig', [
            'formateurs'=>$fr->findAll()
        ]);
    }

    #[Route('/formateur/new', name: 'new_formateur')]
    #[Route('/formateur/{id}/edit', name: 'edit_formateur')] // si on veux rajouter l'edition 
    public function new_edit(Formateur $formateur =null ,Request $request , EntityManagerInterface $entityManager):Response {

	if(!$formateur ){$formateur = new Formateur();} //////////// crée une nouvelle formateur ( un objet pas dans la bdd ) } // si on veux rajouter l'edition sinon on enleve le if mais on garde l'interrieur
        
        $form = $this->createForm(FormateurType::class, $formateur); ////// ( crée le formulaire )

 	$form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() ){ // si le form est submit et qu'il est valide
            $formateur = $form->getData(); //on met les info dans l'entité formateur crée plus haut  
            $entityManager->persist($formateur); // prepare en pdo 
            $entityManager->flush(); // execute en pdo

            return $this->redirectToRoute('app_formateur');
        }


        return $this->render('formateur/new.html.twig',[  //////// ( envoie du form dans la vue ) 
            'FormAddFormateur'=> $form  ,
        ]);
    } 
    #[Route('/formateur/{id}/delete', name: 'delete_formateur')]
    public function delete(Formateur $formateur , EntityManagerInterface $em){   
        $em->remove($formateur);
        $em->flush();
        return $this->redirectToRoute('app_formateur');
    }


    #[Route('/formateur/{id}', name: 'show_formateur')]
    public function show(Formateur $formateur , FormateurRepository $fr): Response
    {   
        
        return $this->render('formateur/show.html.twig', [
            'formateur'=>$fr->find($formateur->getId())
        ]);
    }
}
