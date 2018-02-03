<?php

namespace CriptimBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\Type\RegistrationFormType;
use Domain\UseCase\Request\CreateUserRequest;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

/**
 * Class AuthController
 * @package CriptimBundle\Controller
 */
class AuthController extends Controller
{

	/**
	 * @Route("/registration", name="criptim.auth.registration")
	 * @param Request $request
	 * @return \Domain\UseCase\Response\CreateUserResponse|null|RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function registrationAction(Request $request)
	{
		if ($this->getUser()) {
			return $this->redirectToRoute('criptim.homepage');
		}
		/** @var $userManager UserManagerInterface */
		$userManager = $this->get('fos_user.user_manager');
		/** @var $dispatcher EventDispatcherInterface */
		$dispatcher = $this->get('event_dispatcher');

		/** @var User $user */
		$user = $userManager->createUser();
		$user->setEnabled(true);

		$event = new GetResponseUserEvent($user, $request);
		$dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

		if (null !== $event->getResponse()) {
			return $event->getResponse();
		}

		$form = $this->createForm(RegistrationFormType::class, $user, [
			'validation_groups' => ['AppRegistration', 'Default']
		]);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			if ($form->isValid()) {
				$event = new FormEvent($form, $request);
				$response = $this->get('UseCase\CreateUserUseCase')->execute(new CreateUserRequest());
				$user->setDomainUserId($response->getUser()->getId());
				$user->addRole(User::ROLE_INVESTOR);
				$dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
				$userManager->updateUser($user);
				$response = $event->getResponse();
				if (null === $response) {
					$url = $this->generateUrl('fos_user_registration_confirmed');
					$response = new RedirectResponse($url);
				}
				$dispatcher
					->dispatch(
						FOSUserEvents::REGISTRATION_COMPLETED,
						new FilterUserResponseEvent($user, $request, $response)
					);

				return $this->redirectToRoute('criptim.homepage');
			}

			$event = new FormEvent($form, $request);
			$dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

			if (null !== $response = $event->getResponse()) {
				return $response;
			}
		}

		return $this->render(
			'@Criptim/Auth/registration.html.twig',
			[
				'form' => $form->createView(),
				'layout_title' => 'Регистрация'
			]
		);
	}

	/**
	 * @Route("/login", name="criptim.auth.login")
	 */
	public function loginAction(Request $request)
	{
		if ($this->getUser()) {
			return $this->redirectToRoute('criptim.homepage');
		}
		/** @var $session \Symfony\Component\HttpFoundation\Session\Session */
		$session = $request->getSession();

		$authErrorKey = Security::AUTHENTICATION_ERROR;
		$lastUsernameKey = Security::LAST_USERNAME;

		// get the error if any (works with forward and redirect -- see below)
		if ($request->attributes->has($authErrorKey)) {
			$error = $request->attributes->get($authErrorKey);
		} elseif (null !== $session && $session->has($authErrorKey)) {
			$error = $session->get($authErrorKey);
			$session->remove($authErrorKey);
		} else {
			$error = null;
		}

		if (!$error instanceof AuthenticationException) {
			$error = null; // The value does not come from the security component.
		}

		// last username entered by the user
		$lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

		$csrfToken = $this->has('security.csrf.token_manager')
			? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
			: null;
		return $this->render(
			'@Criptim/Auth/signin.html.twig',
			[
				'last_username' => $lastUsername,
				'error' => $error,
				'csrf_token' => $csrfToken,
				'layout_title' => 'Авторизация'
			]
		);
	}
}
