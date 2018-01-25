<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/25/18
 * Time: 10:53 AM
 */

namespace ControlBundle\Controller;


use ControlBundle\Form\Type\CreateBotRequestFormType;
use Domain\Exchange\Entity\Bot;
use Domain\Exchange\UseCase\Request\CreateBotRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/bots")
 */
class BotsController extends Controller
{
	/**
	 * @Route("", name="control.bots.list")
	 */
	public function listAction(Request $request)
	{
		/** @var Bot[] $bots */
		$bots = $this->get('ORM\BotRepository')->findAll();

		return $this->render('@Control/Bots/list.html.twig', [
			'bots' => $bots
		]);
	}

	/**
	 * @Route("/create", name="control.bots.create")
	 */
	public function createAction(Request $request)
	{
		$form = $this->createForm(CreateBotRequestFormType::class);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			/** @var CreateBotRequest $request */
			$request = $form->getData();
			$response = $this->get('UseCase\CreateBotUseCase')->execute($request);
			$this->addFlash('info', 'Bot created');
			return $this->redirectToRoute('control.bots.list');
		}

		return $this->render('@Control/Bots/new.html.twig', [
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/edit/{id}", name="control.bots.edit")
	 */
	public function editAction(Request $request, $id)
	{
		$form = $this->createForm(CreateBotRequestFormType::class);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			/** @var CreateBotRequest $request */
			$request = $form->getData();
			$response = $this->get('UseCase\CreateBotUseCase')->execute($request);
			$this->addFlash('info', 'Bot created');
			return $this->redirectToRoute('control.bots.list');
		}

		return $this->render('@Control/Bots/new.html.twig', [
			'form' => $form->createView()
		]);
	}
}