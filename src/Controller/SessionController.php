<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Session;
use App\Entity\Programme;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use App\Repository\ProgrammeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(SessionRepository $sr): Response
    {

        return $this->render('session/index.html.twig', [
            'sessions' => $sr->findAll()
        ]);
    }

#[Route('/session/new', name: 'new_session')]
#[Route('/session/{id}/edit', name: 'edit_session')] // si on veux rajouter l'edition
public function new_edit(session $session =null ,Request $request , EntityManagerInterface $entityManager):Response {

if(!$session ){$session = new session(); //////////// crée une nouvelle session ( un objet pas dans la bdd ) } // si on veux rajouter l'edition sinon on enleve le if mais on garde l'interrieur
    
    $form = $this->createForm(SessionType::class, $session); ////// ( crée le formulaire )

$form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid() ){ // si le form est submit et qu'il est valide
        $session = $form->getData(); //on met les info dans l'entité session crée plus haut
        foreach($session->getStagiaires() as $stagiaire) {
            $stagiaire->addSession($session);
            $entityManager->persist($stagiaire);
           
        }
        $entityManager->persist($session); // prepare en pdo 
        $entityManager->flush(); // execute en pdo

        return $this->redirectToRoute('app_session');
    }


    return $this->render('Session/new.html.twig',[  //////// ( envoie du form dans la vue ) 
        'formAddSession'=> $form  ,
    ]);
} }


    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session , SessionRepository $sr , ProgrammeRepository $pr): Response
    {
        $programmes= $pr->findBy(
            ['session' => $session]
        );

        //trie des modules par categorie , en definitant la fonction de trie a l'interrieur du usort 
        usort($programmes,  function ( Programme $a, Programme $b) {
            if ($a->getModule()->getCategorie()->getNom() == $b->getModule()->getCategorie()->getNom()) {
                return 0;
            }
            return ($a->getModule()->getCategorie()->getNom() < $b->getModule()->getCategorie()->getNom()) ? -1 : 1;
        });



        return $this->render('session/show.html.twig', [
            'session' => $sr->find($session->getId()),
            'programmes'=> $programmes
        ]);
    }
}
