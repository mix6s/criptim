<?php

namespace ControlBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

/**
 * Class AuthController
 * @package ControlBundle\Controller
 */
class AuthController extends Controller
{
	/**
	 * @Route("/login", name="control.auth.login")
	 */
	public function loginAction(Request $request)
	{
		if ($this->getUser()) {
			return $this->redirectToRoute('control.index');
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
			'@Control/Auth/login.html.twig',
			[
				'last_username' => $lastUsername,
				'error' => $error,
				'csrf_token' => $csrfToken,
				'layout_title' => 'Авторизация'
			]
		);
	}
}
