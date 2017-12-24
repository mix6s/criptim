<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 27.07.2017
 * Time: 16:37
 */

namespace ControlBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ControlController
 * @package ControlBundle\Controller
 */
class ControlController extends Controller
{
	/**
	 * @Route("", name="control.index")
	 */
	public function indexAction()
	{
		return;
	}
}