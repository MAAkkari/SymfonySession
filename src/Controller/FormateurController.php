<?php

namespace App\Controller;

use App\Entity\Formateur;
use App\Repository\FormateurRepository;
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
    #[Route('/formateur/{id}', name: 'show_formateur')]
    public function show(Formateur $formateur , FormateurRepository $fr): Response
    {   
        
        return $this->render('formateur/show.html.twig', [
            'formateur'=>$fr->find($formateur->getId())
        ]);
    }
}
