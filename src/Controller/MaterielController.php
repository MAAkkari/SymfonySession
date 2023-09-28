<?php

namespace App\Controller;

use App\Entity\Materiel;
use App\Entity\Stagiaire;
use App\Form\MaterielType;
use App\Repository\MaterielRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
            'materiels' => $materiels
        ]);
    }

    #[Route('/materiel/new', name: 'new_materiel')]
    public function new_edit(Request $request , EntityManagerInterface $entityManager){

	$materiel = new materiel();  //////////// crée une nouvelle materiel ( un objet pas dans la bdd )  // si on veux rajouter l'edition sinon on enleve le if mais on garde l'interrieur
        
        $form = $this->createForm(MaterielType::class, $materiel); ////// ( crée le formulaire )

 	$form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() ){ // si le form est submit et qu'il est valide
            $materiel = $form->getData(); //on met les info dans l'entité materiel crée plus haut
            
            $entityManager->persist($materiel); // prepare en pdo 
            $entityManager->flush(); // execute en pdo

            return $this->redirectToRoute('app_materiel');
        }


        return $this->render('materiel/new.html.twig',[  //////// ( envoie du form dans la vue ) 
            'formAddMateriel'=> $form  ,
        ]);
   }

   #[Route('/materiel/{id}/delete', name: 'delete_materiel')]
    public function delete(materiel $materiel , EntityManagerInterface $em){   
        $em->remove($materiel);
        $em->flush();
        return $this->redirectToRoute('app_materiel');

    }
}
