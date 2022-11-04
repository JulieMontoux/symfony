<?php

namespace App\Controller;

use App\Entity\Acteur;
use App\Repository\ActeurRepository;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActeurController extends AbstractController
{
    /**
     * @Route("/acteur", name="app_acteur")
     */
    public function index(): Response
    {
        $acteur = $this->getDoctrine()
        ->getRepository(Acteur::class)
        ->findAll();
    return $this->render('acteur/acteur.html.twig', [
        'controller_name' => 'ActeurController',
        'acteur'          => $acteur
    ]);
    }
/**
     * @Route("/acteur/create", name="app_acteur_create")
     */

    public function create(Request $request): Response
    {
        if ($request->isMethod("POST")) {
            $acteur              = new Acteur;
            $manager = $this->getDoctrine()->getManager();
            $file = $request->files->get('photo');
            $newFilename = $file->getClientOriginalName();
               
            $file->move($this->getParameter('image_directory'),$newFilename);

            // Insertion en BDD
            $acteur->setNom($request->request->get('nom'))
                ->setPrenom($request->request->get('prenom'))
                ->setNaissance(
                    \DateTime::createFromFormat('Y-m-d', $request->request->get('naissance'))
                )
                ->setDeces(
                    \DateTime::createFromFormat('Y-m-d', $request->request->get('deces'))?: null
                );
           
       
      
         
                 
                
                    
                $acteur->setPhoto($newFilename);
              
             
           $manager->persist($acteur);
                $manager->flush();
                return $this->redirectToRoute('app_acteur');
            
        }
                    // Affichage du formulaire
            return $this->render('acteur/create.html.twig', [
                'controller_name' => 'ActeurController',
            ]);
        
    }


    /**
     * @Route("/acteur/{acteur}/edit", name="app_acteur_edit")
     */
    public function edit(Request $request, Acteur $acteur): Response
    {
        if ($request->isMethod("POST")) {
            $manager = $this->getDoctrine()->getManager();
            // Insertion en BDD
            $acteur->setNom($request->request->get('nom'))
            ->setPrenom($request->request->get('prenom'))
            ->setNaissance(
                \DateTime::createFromFormat('Y-m-d', $request->request->get('naissance'))
            )
            ->setDeces(
                \DateTime::createFromFormat('Y-m-d', $request->request->get('deces')?:Null)
            )
            ->setPhoto($request->request->get('photo'));

            $manager->flush();

            return $this->redirectToRoute('app_acteur');
        } else {
            $acteurs = $this->getDoctrine()
                ->getRepository(Acteur::class)
                ->findAll();
            
            // Affichage du formulaire
            return $this->render('acteur/edit.html.twig', [
                'controller_name' => 'ActeurController',
                'acteurs'           => $acteurs,
                'acteur'=>$acteur
            ]);
        }
    }

    /**
     * @Route("/acteur/{acteur}/delete", name="app_acteur_delete")
     */
    public function delete(Request $request, Acteur $acteur): Response
    {
        $this->getDoctrine()->getManager()->remove($acteur);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('app_acteur');
    }

    /**
     * @Route("/acteur/{id}", name="app_acteur_show")
     */
    public function show(Acteur $acteur): Response
    {
        return $this->render('acteur/show.html.twig', [
            'acteur' => $acteur,
        ]);
}
}