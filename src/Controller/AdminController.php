<?php

namespace App\Controller;

use App\Form\EditUserFormType;
use App\Form\EditUserRootFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
	private UserRepository $userRepository;
	private EntityManagerInterface $entityManager;

	public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository){
		$this->userRepository = $userRepository;
		$this->entityManager = $entityManager;
	}
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

	#[Route('/admin/users', name: 'app_admin_users')]
	public function users(): Response
	{
		$query = $this->entityManager->createQueryBuilder();
		$query->select('u')
			->from('App\Entity\User', 'u')
			->where('u.roles NOT LIKE :adminRole')
			->andwhere('u.roles NOT LIKE :rootRole')
			->setParameter('adminRole', '%ROLE_ADMIN%')
			->setParameter('rootRole', '%ROLE_ROOT%');

		$users = $query->getQuery()->getResult();
		return $this->render('admin/users.html.twig', ['users' => $users]);
	}

	#[Route('/admin/users/{id}', name: 'app_admin_user')]
	public function user($id, Request $request): Response
	{
		$user = $this->userRepository->find($id);

		$form = $this->createForm(EditUserFormType::class, $user);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$this->entityManager->persist($user);
			$this->entityManager->flush();
			return $this->redirectToRoute('app_admin_users');
		}

		return $this->render('admin/edit_user.html.twig', ['users' => $user, 'form' => $form->createView()]);
	}

	#[Route('/admin/users_root', name: 'app_admin_users_root')]
	public function usersRoot(): Response
	{
		$query = $this->entityManager->createQueryBuilder();
		$query->select('u')
			->from('App\Entity\User', 'u')
			->andwhere('u.roles NOT LIKE :rootRole')
			->setParameter('rootRole', '%ROLE_ROOT%');

		$users = $query->getQuery()->getResult();
		return $this->render('admin/users.html.twig', ['users' => $users]);
	}

	#[Route('/admin/users_root/{id}', name: 'app_admin_user_root')]
	public function userRoot($id, Request $request): Response
	{
		$user = $this->userRepository->find($id);

		$form = $this->createForm(EditUserRootFormType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$this->entityManager->persist($user);
			$this->entityManager->flush();
			return $this->redirectToRoute('app_admin_users_root');
		}

		return $this->render('admin/edit_user_root.html.twig', ['user' => $user, 'form' => $form->createView()]);
	}
}
