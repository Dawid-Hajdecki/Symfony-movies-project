<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommentController extends AbstractController
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

    #[Route('/movies/{movieId}/comment/create', name: 'app_comment_create')]
    public function create($movieId, Request $request): Response
    {
		$movie = $this->movieRepository->find($movieId);
		if(!$movie){
			return $this->redirectToRoute('movies');
		}
		$comment = new Comment();
		$form = $this->createForm(CommentFormType::class, $comment);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$comment->setMovieId($movie);
			$comment->setUserId($this->currentUser);

			$this->entityManager->persist($comment);
			$this->entityManager->flush();
			return $this->redirectToRoute('movie', ['id' => $movieId]);
		}

        return $this->render('comment/create.html.twig', ['form' => $form->createView(), 'movieId' => $movieId]);
    }

    #[Route('/movies/{movieId}/comment/edit/{id}', name: 'app_comment_edit')]
    public function edit($movieId, $id, Request $request): Response
    {
		$movie = $this->movieRepository->find($movieId);
		if(!$movie){
			return $this->redirectToRoute('movies');
		}
		$comment = $this->commentRepository->find($id);
		if(!$comment){
			return $this->redirectToRoute('movie', ['id' => $movieId]);
		}
		if($comment->getUser() !== $this->currentUser && !$this->isGranted('ROLE_ROOT')){
			return $this->redirectToRoute('movie', ['id' => $movieId]);
		}
		$form = $this->createForm(CommentFormType::class, $comment);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$this->entityManager->persist($comment);
			$this->entityManager->flush();
			return $this->redirectToRoute('movie', ['id' => $movieId]);
		}

        return $this->render('comment/edit.html.twig', ['form' => $form->createView(), 'movieId' => $movieId]);
    }

	#[Route('/movies/{movieId}/comment/delete/{id}', name: 'app_comment_delete')]
	public function delete($movieId, $id): Response
	{
		$comment = $this->commentRepository->find($id);
		if($comment->getUser() !== $this->currentUser && !$this->isGranted('ROLE_ADMIN')){
			return $this->redirectToRoute('movie', ['id' => $movieId]);
		}
		if($comment){
			$this->entityManager->remove($comment);
			$this->entityManager->flush();
		}
		return $this->redirectToRoute('movie', ['id' => $movieId]);
	}
}
