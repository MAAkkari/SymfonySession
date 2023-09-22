<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Stagiaire;
use App\Form\StagiaireType;
use App\Repository\StagiaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StagiaireController extends AbstractController
{
    #[Route('/stagiaire', name: 'app_stagiaire')]
    public function index(StagiaireRepository $sr): Response
    {   
        
        return $this->render('stagiaire/index.html.twig', [
            'stagiaires'=>$sr->findAll()
        ]);
    }

   
    #[Route('/stagiaire/new', name: 'new_stagiaire')]
    #[Route('/stagiaire/{id}/edit', name: 'edit_stagiaire')] // rajouter l'edition 
    public function new_edit(stagiaire $stagiaire =null ,Request $request , EntityManagerInterface $entityManager):Response {

	if(!$stagiaire ){$stagiaire = new stagiaire(); } // crée un nouveau stagiaire ( un objet pas dans la bdd )  // si on veux rajouter l'edition sinon on enleve le if mais on garde l'interrieur
        
        $form = $this->createForm(StagiaireType::class, $stagiaire); // ( crée le formulaire )

 	$form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() ){ // si le form est submit et qu'il est valide
            $stagiaire = $form->getData(); //on met les info dans l'entité stagiaire crée plus haut
            
            
           
            foreach($stagiaire->getSessions() as $session) {
                $session->addStagiaire($stagiaire);
                $entityManager->persist($session);
               
            }
            $entityManager->persist($stagiaire); // prepare en pdo
            $entityManager->flush(); // execute en pdo
            
            return $this->redirectToRoute('app_stagiaire');
        }


        return $this->render('stagiaire/new.html.twig',[  //////// ( envoie du form dans la vue ) 
            'formAddStagiaire'=> $form  ,
        ]);
    } 
    #[Route('/stagiaire/{id}/delete', name: 'delete_stagiaire')]
    public function delete(Stagiaire $stagiaire , EntityManagerInterface $em){   
        $em->remove($stagiaire);
        $em->flush();
        return $this->redirectToRoute('app_stagiaire');

    }

    #[Route('/stagiaire/{id}', name: 'show_stagiaire')]
    public function show(Stagiaire $stagiaire , StagiaireRepository $sr): Response
    {   
        
        return $this->render('stagiaire/show.html.twig', [
            'stagiaire'=>$sr->find($stagiaire->getId()),
            
        ]);
    }
}
