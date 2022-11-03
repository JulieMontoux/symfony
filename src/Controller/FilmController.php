<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Genre;
use App\Repository\FilmRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FilmController extends AbstractController
{
    /**
    * @Route("/film", name="app_film")
    */
    public function index(): Response
    {
        $film = $this->getDoctrine()
            ->getRepository(Film::class)
            ->findAll();
        return $this->render('film/film.html.twig', [
            'controller_name' => 'FilmController',
            'film'          => $film
        ]);
    }

   /**
     * @Route("/film/{id}", name="app_film_show")
     */

    public function show(Film $film): Response
    {
        return $this->render('film/show.html.twig', [
            'film' => $film,
        ]);
    }

    /**
     * @Route("/film/create", name="app_film_create")
     */

    public function create(Request $request): Response
    {
        if ($request->isMethod("POST")) {
            $manager = $this->getDoctrine()->getManager();

            // Insertion en BDD
            $film = new Film;
            $film->setTitre($request->request->get('titre'))
                ->setResume($request->request->get('resume'))
                ->setSortie(
                    \DateTime::createFromFormat('Y-m-d', $request->request->get('sortie'))
                )
                
                ->setAffiche($request->request->get('affiche'));

            $genre = $this->getDoctrine()
                ->getRepository(Genre::class)
                ->find($request->request->get('genre_id'));
            $film->setGenre($genre);

            $manager->persist($film);
            $manager->flush();

            $file = $request->files->get('affiche');
            if ($file) {
                $newFilename = 'film-' . $film->getId() . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        'affiches',
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $film->setAffiche('affiches/' . $newFilename);
                $manager->flush();
            }

            return $this->redirectToRoute('app_film');
        } else {
            $genres = $this->getDoctrine()
                ->getRepository(Genre::class)
                ->findAll();
            // Affichage du formulaire
            return $this->render('film/create.html.twig', [
                'controller_name' => 'FilmController',
                'genres' => $genres
            ]);
        }
    }

    /**
     * @Route("/film/{film}/edit", name="app_film_edit")
     */
    public function edit(Request $request, Film $film): Response
    {
        if ($request->isMethod("POST")) {
            $manager = $this->getDoctrine()->getManager();
            // Insertion en BDD
            $film->setTitre($request->request->get('titre'))
                ->setResume($request->request->get('resume'))
                ->setSortie(
                \DateTime::createFromFormat('Y-m-d', $request->request->get('sortie')))
                ->setGenre($request->request->get('genre_id'))
                ->setAffiche($request->request->get('affiche'));

            $genre = $this->getDoctrine()
                ->getRepository(Genre::class)
                ->find($request->request->get('genre_id'));
            $film->setGenre($genre);

            $manager->flush();

            return $this->redirectToRoute('app_film');
        } else {
            $genres = $this->getDoctrine()
                ->getRepository(Genre::class)
                ->findAll();
            // Affichage du formulaire
            return $this->render('film/edit.html.twig', [
                'controller_name' => 'FilmController',
                'film'           => $film,
                'genres'        => $genres
            ]);
        }
    }

    /**
     * @Route("/film/{film}/delete", name="app_film_delete")
     */
    public function delete(Request $request, Film $film): Response
    {
        $this->getDoctrine()->getManager()->remove($film);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('app_film');
    }

}