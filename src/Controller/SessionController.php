<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Session;
use App\Entity\Utiliser;
use App\Entity\Programme;
use App\Form\SessionType;
use App\Form\UtiliserType;
use App\Form\ProgrammeType;
use App\Repository\ModuleRepository;
use App\Repository\SessionRepository;
use App\Repository\ProgrammeRepository;
use App\Repository\StagiaireRepository;
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

if(!$session){$session = new session(); } //////////// crée une nouvelle session ( un objet pas dans la bdd )  // si on veux rajouter l'edition sinon on enleve le if mais on garde l'interrieur
    
    $form = $this->createForm(SessionType::class, $session); ////// ( crée le formulaire )

$form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid() ){ // si le form est submit et qu'il est valide
        $session = $form->getData(); //on met les info dans l'entité session crée plus haut
        if ( count($session->getStagiaires()) > $session->getPlaces()){
            $this->addFlash("error","nombre de place insuffisant par rapport au nombre de stagiaires");
            return $this->redirectToRoute('new_session');
        }else{
        foreach($session->getStagiaires() as $stagiaire) {
            
            $stagiaire->addSession($session);
            $entityManager->persist($stagiaire);
           
            return $this->redirectToRoute('app_session');
        }
        $entityManager->persist($session); // prepare en pdo 
        $entityManager->flush(); // execute en pdo
        $this->addFlash("success","creation/modification de la session avec succes");
        return $this->redirectToRoute('app_session');
    }}


    return $this->render('Session/new.html.twig',[  //////// ( envoie du form dans la vue ) 
        'formAddSession'=> $form  ,
    ]);
}

    #[Route('/session/{id}/delete', name: 'delete_session')]
    public function delete(Session $session, Request $request, EntityManagerInterface $em ){
        $em->remove($session);
        $em->flush();
        $this->addFlash("success","suppression de la session avec succes");
        return $this->redirectToRoute('app_session');
    }

    #[Route('/session/addModule/{id}/{module}', name: 'add_module')]
    public function addModule(Session $session, Module $module, Request $request, EntityManagerInterface $entityManager)
    {
        $nbJours = $request->request->get('duree');

        $dateDebut = $session->getDebut();
        $dateFin = $session->getFin();
        $interval = $dateDebut->diff($dateFin);
        $joursDifference = $interval->days;
        $total=0;
        foreach($session->getProgrammes() as $programme){
            $total+=$programme->getJours();
        }
        if($joursDifference < $total+$nbJours){
            $this->addFlash("error","le nombre de jours excede la durée total de la formation");
        }elseif($nbJours != null) { 
            $programme = new Programme();
            // On ajoute le programme à la session et la session au programme
            $programme->setSession($session);
            // On ajoute le module au programme et le programme au module
            $programme->setModule($module);
            // On ajoute le nombre de jours au programme
            $programme->setJours($nbJours);

            // Persiste les modifications
            $entityManager->persist($programme);
            // Enregistre les modifications
            $entityManager->flush();

            // Message flash de succès
            $this->addFlash('success', 'Ajout du Module Reussi');
        }
        return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
    }


    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session ,ModuleRepository $mr, SessionRepository $sr , ProgrammeRepository $pr ,EntityManagerInterface $entityManager ,Request $request, StagiaireRepository $StagiaireRepo): Response
    {
        
        $id=$session->getId();
        $utiliser = new Utiliser();
        $form = $this->createForm(UtiliserType::class, $utiliser);
        $form->handleRequest($request);
        

        if($form->isSubmitted() && $form->isValid() ){ // si le form est submit et qu'il est valide
            
            $utiliser = $form->getData(); //on met les info dans l'entité utiliser crée plus haut
            $present = false;
            foreach($session->getUtilisers() as $utiliserSession ){
                if ( $utiliserSession->getMateriel() == $utiliser->getMateriel() ){ 

                    $newQtt=$utiliserSession->getQtt() + $utiliser->getQtt();
                    
                    if($newQtt > $utiliser->getMateriel()->getQtt()){
                        $this->addFlash("error","la Quantité de  ''".$utiliser->getMateriel()->getNom()."'' est insuffisante");
                        return $this->redirectToRoute('show_session', ['id' => $id]);
                    }else{
                        $utiliserSession->setQtt($newQtt);
                        $entityManager->persist($utiliserSession); // prepare en pdo
                        $entityManager->flush(); // execute en pdo
                        $present= true;
                        $this->addFlash("success","ajout des ''".$utiliser->getMateriel()->getNom()."'' reussi ");
                    }
                }
            }

            if($present == false){
            
                $utiliser->setSession($session);
                $entityManager->persist($utiliser); // prepare en pdo
                $entityManager->flush(); // execute en pdo
                $this->addFlash("success","ajout des ''".$utiliser->getMateriel()->getNom()."'' reussi ");
            }
            return $this->redirectToRoute('show_session', ['id' => $id]);
        }

        $programmes= $pr->findBy(
          ['session' => $session]
        );

        $sessionId=$session->getId();
        $stagiaires = $sr->findNonInscrits($sessionId);
        $NonProgrammes = $sr->findNonProgrammes($sessionId);
        

        

        //trie des modules par categorie , en definisant la fonction de trie a l'interrieur du usort 
        usort($programmes,  function (Programme $a, Programme $b) {
            if ($a->getModule()->getCategorie()->getNom() == $b->getModule()->getCategorie()->getNom()) {
                return 0;
            }
            return ($a->getModule()->getCategorie()->getNom() < $b->getModule()->getCategorie()->getNom()) ? -1 : 1;
        });

        return $this->render('session/show.html.twig', [
            'session' => $sr->find($session->getId()),
            'programmes'=> $programmes,
            'NonProgrammes'=> $NonProgrammes,
            'stagiaires'=>$stagiaires,
            'formAddUtiliser'=>$form
        ]);
    }
}
