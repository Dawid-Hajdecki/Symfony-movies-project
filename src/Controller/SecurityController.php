<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ProfileFormType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
	private UserRepository $userRepository;
	private EntityManagerInterface $entityManager;

	public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
	{
		$this->userRepository = $userRepository;
		$this->entityManager = $entityManager;
	}
	#[Route('/register', name: 'app_register')]
	public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
	{
		//redirect if user logged in
		if ($this->getUser()) {
			return $this->redirectToRoute('app_root');
		}
		$user = new User();
		$form = $this->createForm(RegistrationFormType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// encode the plain password
			$user->setPassword(
				$userPasswordHasher->hashPassword(
					$user,
					$form->get('plainPassword')->getData()
				)
			);

			$entityManager->persist($user);
			$entityManager->flush();

			// do anything else you need here, like send an email

			return $this->redirectToRoute('app_login');
		}

		return $this->render('security/register.html.twig', [
			'registrationForm' => $form,
		]);
	}

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
		//redirect if user logged in
		if ($this->getUser()) {
			return $this->redirectToRoute('app_root');
		}

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

	#[Route(path: '/profile', name: 'app_profile')]
	public function profile(Request $request): Response
	{
		$user = $this->getUser();
		$form = $this->createForm(ProfileFormType::class, $user);

		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()){
			$this->entityManager->persist($user);
			$this->entityManager->flush();

			return $this->redirectToRoute('app_root');
		}
		return $this->render('security/profile.html.twig', ['form' => $form->createView()]);
	}

	#[Route(path: '/profile/changePassword', name: 'app_change_password')]
	public function changePassword(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
	{
		$user = $this->getUser();
		$form = $this->createForm(ChangePasswordFormType::class, $user);

		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()){
			if($userPasswordHasher->isPasswordValid($user, $form->get('currentPassword')->getData())){
				$user->setPassword(
					$userPasswordHasher->hashPassword(
						$user,
						$form->get('password')->getData()
					)
				);
				$this->entityManager->persist($user);
				$this->entityManager->flush();

				return $this->redirectToRoute('app_root');
			}else{
				return $this->render('security/change_password.html.twig', ['form' => $form->createView(), 'error' => 'Wrong current password. Please try again.']);
			}
		}

		return $this->render('security/change_password.html.twig', ['form' => $form->createView(), 'error' => null]);
	}

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
