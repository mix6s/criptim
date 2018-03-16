<?php


namespace FintobitBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
}