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
 * Class DefaultController
 * @package ControlBundle\Controller
 * @Security("has_role('ROLE_CONTROL')")
 */
class DefaultController extends Controller
{
	/**
	 * @Route("", name="control.index")
	 */
	public function indexAction()
	{
		return $this->render('@Control/Default/index.html.twig');
	}
}