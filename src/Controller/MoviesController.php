<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieFormType;
use App\Repository\CommentRepository;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MoviesController extends AbstractController
{
    private MovieRepository $movieRepository;
    private CommentRepository $commentRepository;
	private EntityManagerInterface $entityManager;
    public function __construct(MovieRepository $movieRepository, EntityManagerInterface $entityManager, Security $security, CommentRepository $commentRepository){
        $this->movieRepository = $movieRepository;
        $this->commentRepository = $commentRepository;
		$this->entityManager = $entityManager;
		$this->currentUser = $security->getUser();
    }

	#[Route('/', name: 'app_root')]
	public function main(): Response
	{
		return $this->render('index.html.twig');
	}

    #[Route('/movies', name: 'movies', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('movies/index.html.twig', [
            'movies' => $this->movieRepository->findAll()
        ]);
    }

    #[Route('/movies/create', name: 'movies_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
	{
		if(!$this->isGranted('ROLE_ADMIN')){
			return $this->redirectToRoute('movies');
		}
        $movie = new Movie();
        $form = $this->createForm(MovieFormType::class, $movie);

		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()){
			$newMovie = $form->getData();

			$movie->setUserId($this->currentUser);

			$imagePath = $form->get('imagePath')->getData();
			if($imagePath){
				$newFileName = uniqid().'.'.$imagePath->guessExtension();

				try{
					$imagePath->move($this->getParameter('kernel.project_dir').'/public/uploads', $newFileName);
				} catch (FileException $e) {
					return new Response($e->getMessage());
				}

				$newMovie->setImagePath('uploads/'.$newFileName);
			}

			$this->entityManager->persist($newMovie);
			$this->entityManager->flush();

			return $this->redirectToRoute('movies');
		}
        return $this->render('movies/create.html.twig', ['form' => $form->createView()]);
    }

	#[Route('/movies/edit/{id}', name: 'movies_edit', methods: ['GET', 'POST'])]
	public function edit($id, Request $request, Security $security): Response
	{
		if(!$this->isGranted('ROLE_ADMIN')){
			return $this->redirectToRoute('movies');
		}
		$movie = $this->movieRepository->find($id);
		if(!$movie){
			return $this->redirectToRoute('movies');
		}
		$form = $this->createForm(MovieFormType::class, $movie);

		$form->handleRequest($request);
		$imagePath = $form->get('imagePath')->getData();

		if($form->isSubmitted() && $form->isValid()){
			$movie->setUserId($this->currentUser);
			if($imagePath){
				if($movie->getImagePath()){
					//dd(file_exists($movie->getImagePath()));
					//if(file_exists($this->getparameter('kernel.project_dir').'/public/'.$movie->getImagePath())){
					$this->getParameter('kernel.project_dir').$movie->getImagePath();

					$newFileName = uniqid().'.'.$imagePath->guessExtension();

					try{
						$imagePath->move($this->getParameter('kernel.project_dir').'/public/uploads', $newFileName);
					} catch (FileException $e) {
						return new Response($e->getMessage());
					}

					$movie->setImagePath('uploads/'.$newFileName);
					$this->entityManager->persist($movie);
					$this->entityManager->flush();

					return $this->redirectToRoute('movie', ['id' => $id]);
					//}
				}
			}else{
				$this->entityManager->persist($movie);
				$this->entityManager->flush();

				return $this->redirectToRoute('movie', ['id' => $id]);
			}
		}
		return $this->render('movies/edit.html.twig', ['movie' => $movie, 'form' => $form->createView()]);
	}

	#[Route('/movies/delete/{id}', name: 'movies_delete', methods: ['GET', 'DELETE'])]
	public function delete($id): Response
	{
		if(!$this->isGranted('ROLE_ADMIN')){
			return $this->redirectToRoute('movies');
		}
		$movie = $this->movieRepository->find($id);
		if($movie) {
			$this->entityManager->remove($movie);
			$this->entityManager->flush();
		}
		return $this->redirectToRoute('movies');
	}

    #[Route('/movies/{id}', name: 'movie', methods: ['GET'])]
    public function show($id): Response
    {
		$movie = $this->movieRepository->findOneBy(['id' => $id]);
		$comments = $this->commentRepository->findBy(['MovieId' => $movie]);

		if(!$movie){
			return $this->redirectToRoute('movies');
		}else{
			return $this->render('movies/show.html.twig', ['movie' => $movie, 'comments' => $comments, 'currentUser' => $this->currentUser]);
		}
    }
}
