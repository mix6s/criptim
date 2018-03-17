<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 27.07.2017
 * Time: 16:37
 */

namespace ControlBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class IndexController
 * @package ControlBundle\Controller
 * @Security("has_role('ROLE_CONTROL')")
 */
class IndexController extends Controller
{
	/**
	 * @Route("/", name="control.index")
	 */
	public function indexAction()
	{
		if ($this->getUser()) {
			return $this->forward('ControlBundle:Users:list');
		}
		return $this->forward('ControlBundle:Auth:login');
	}
}