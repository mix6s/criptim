<?php


namespace FintobitBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InviteController extends Controller
{

	/**
	 * @Route("/invite", name="fintobit.invite.index")
	 * @return Response
	 */
	public function inviteAction(): Response
	{
		if ($this->getUser()) {
			return $this->redirectToRoute('fintobit.profile.index');
		}
		return $this->render('@Fintobit/Invite/index.html.twig');
	}

	/**
	 * @Route("/invite/success", name="fintobit.invite.success")
	 * @return Response
	 */
	public function successAction(): Response
	{
		if ($this->getUser()) {
			return $this->redirectToRoute('fintobit.profile.index');
		}
		return $this->render('@Fintobit/Invite/success.html.twig');
	}

	/**
	 * @Route("/invite/submit", name="fintobit.invite.submit", methods={"POST"})
	 * @return Response
	 */
	public function submitAction(Request $request): Response
	{
		$email = $request->request->get('email');
		if (empty($email)) {
			return $this->redirectToRoute('fintobit.invite.success');
		}
		$message = (new \Swift_Message('Invite request'))
			->setFrom('noreply@fintobit.com')
			->setTo('fintobit@yandex.com')
			->setBody(
				$this->renderView('@Fintobit/Invite/email.html.twig', ['email' => $email]),
				'text/html'
			);
		$this->get('swiftmailer.mailer')->send($message);
		return $this->redirectToRoute('fintobit.invite.success');
	}
}