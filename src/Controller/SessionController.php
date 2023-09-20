<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Session;
use App\Entity\Programme;
use App\Repository\SessionRepository;
use App\Repository\ProgrammeRepository;
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
