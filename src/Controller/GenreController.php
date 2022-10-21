<?php

namespace App\Controller;

use App\Entity\Genre;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenreController extends AbstractController
{
    /**
     * @Route("/genre", name="app_genre")
     */
    public function index(): Response
    {
        $genre = $this->getDoctrine()
            ->getRepository(Genre::class)
            ->findAll();
        return $this->render('genre/genre.html.twig', [
            'controller_name' => 'GenreController',
            'genre'          => $genre
        ]);
    }

    /**
     * @Route("/genre/create", name="app_genre_create")
     */

    public function create(Request $request): Response
    {
        if ($request->isMethod("POST")) {
            $manager = $this->getDoctrine()->getManager();

            // Insertion en BDD
            $genre = new Genre;
            $genre->setCategorie($request->request->get('categorie'));

            $manager->persist($genre);
            $manager->flush();

            return $this->redirectToRoute('app_genre');
        } else {
            $genres = $this->getDoctrine()
                ->getRepository(Genre::class)
                ->findAll();
            // Affichage du formulaire
            return $this->render('genre/create.html.twig', [
                'controller_name' => 'GenreController',
                'genres' => $genres
            ]);
        }
    }

    /**
     * @Route("/genre/{genre}/edit", name="app_genre_edit")
     */
    public function edit(Request $request, Genre $genre): Response
    {
        if ($request->isMethod("POST")) {
            $manager = $this->getDoctrine()->getManager();
            // Insertion en BDD
            $genre->setCategorie($request->request->get('categorie'));

            $manager->flush();

            return $this->redirectToRoute('app_genre');
        } else {
            $genres = $this->getDoctrine()
                ->getRepository(Genre::class)
                ->findAll();
            // Affichage du formulaire
            return $this->render('genre/edit.html.twig', [
                'controller_name' => 'GenreController',
                'genres'        => $genres
            ]);
        }
    }

    /**
     * @Route("/genre/{genre}/delete", name="app_genre_delete")
     */
    public function delete(Request $request, Genre $genre): Response
    {
        if ($genre)
        $this->getDoctrine()->getManager()->remove($genre);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('app_genre');
    }
}
