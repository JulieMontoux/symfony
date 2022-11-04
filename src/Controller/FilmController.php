<?php

namespace App\Controller;

use App\Entity\Acteur;
use App\Entity\Film;
use App\Entity\Genre;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    * @Route("/films/create", name="app_film_create")
    */
    public function create(Request $request): Response
    {
        if ($request->isMethod("POST")) {
            $manager = $this->getDoctrine()->getManager();

            $affiche = $request->files->get('affiche');
            $affiche_name = $affiche->getClientOriginalName();
            $affiche->move($this->getParameter('affiche_directory'),$affiche_name);
            // Insertion en BDD
            $film              = new Film;
            $film->setTitre($request->request->get('titre'))
                ->setResume($request->request->get('resume'))
                ->setSortie(
                    \DateTime::createFromFormat('Y-m-d', $request->request->get('sortie'))
                )
                ->setAffiche($affiche_name);

            $genre = $this->getDoctrine()
                ->getRepository(Genre::class)
                ->find($request->request->get('genre'));
            $film->setGenre($genre);

            //Séléction d'un acteur pour le film créé
            $acteurs = $request->request->get('acteurs');
            foreach($acteurs as $acteurID) {
                $acteur = $this->getDoctrine()
                ->getRepository(Acteur::class)
                ->find($acteurID);
                $film->addActeur($acteur);

}
            $manager->persist($film);
            $manager->flush();

            return $this->redirectToRoute('app_film');
        } else {
            $genres = $this->getDoctrine()
                ->getRepository(Genre::class)
                ->findAll();

            $acteurs = $this->getDoctrine()
                ->getRepository(Acteur::class)
                ->findAll();
            // Affichage du formulaire
            return $this->render('film/create.html.twig', [
                'controller_name' => 'FilmController',
                'genres'        => $genres,
                'acteurs'        => $acteurs

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
                    \DateTime::createFromFormat('Y-m-d', $request->request->get('sortie'))
                );

            $genre = $this->getDoctrine()
                ->getRepository(Genre::class)
                ->find($request->request->get('genre'));
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

    /**
     *  @Route("/film/{genreid}/genre", name="liste")
     */
    public function liste(FilmRepository $res ,$genreid): Response
    {
        $films=$res->findByGenre($genreid);
        return $this->render('film/gf.html.twig', [
            'controller_name' => 'FilmController',
            'films'          => $films
        ]);
    }
}