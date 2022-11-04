<?php

namespace App\Controller;

use App\Entity\Acteur;
use App\Repository\ActeurRepository;
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

            // Insertion en BDD
            $acteur->setNom($request->request->get('nom'))
                ->setPrenom($request->request->get('prenom'))
                ->setNaissance(
                    \DateTime::createFromFormat('Y-m-d', $request->request->get('naissance'))
                )
                ->setDeces(
                    \DateTime::createFromFormat('Y-m-d', $request->request->get('deces')) ?: null
                )
                ->setPhoto('photo');
            $manager->persist($acteur);
            $manager->flush();
            $file = $request->files->get('photo');
            if ($file) {
                $newFilename = 'acteur-' . $acteur->getId() . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        'images',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $acteur->setPhoto('images/' . $newFilename);
                $manager->flush();
            }
            return $this->redirectToRoute('app_acteur');
        } else {
            // Affichage du formulaire
            return $this->render('acteur/create.html.twig', [
                'controller_name' => 'ActeurController',
            ]);
        }
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
                \DateTime::createFromFormat('Y-m-d', $request->request->get('deces'))
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