<?php

namespace FintobitBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class IndexController
 * @package Fintobit\Controller
 */
class IndexController extends Controller
{

	/**
	 * @Route("/", name="fintobit.index")
	 */
	public function indexAction()
	{
		if ($this->getUser()) {
			return $this->forward('FintobitBundle:Profile:profile');
		}
		return $this->forward('FintobitBundle:Auth:login');
	}

}