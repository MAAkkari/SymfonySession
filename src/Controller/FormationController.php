<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Repository\FormationRepository;
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

    #[Route('/formation/{id}', name: 'show_formation')]
    public function show(Formation $formation , FormationRepository $fr): Response
    {   
        
        return $this->render('formation/show.html.twig', [
            'formation'=>$fr->find($formation->getId()),
        ]);
    }
}
