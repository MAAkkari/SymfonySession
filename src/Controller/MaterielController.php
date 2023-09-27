<?php

namespace App\Controller;

use App\Repository\MaterielRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MaterielController extends AbstractController
{
    #[Route('/materiel', name: 'app_materiel')]
    public function index(MaterielRepository $mr): Response
    {
        $materiels = $mr->findAll();
        return $this->render('materiel/index.html.twig', [
            'materiels' => $materiels,
        ]);
    }
}
