<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/25/18
 * Time: 10:53 AM
 */

namespace ControlBundle\Controller;


use ControlBundle\Form\Type\CreateBotRequestFormType;
use ControlBundle\Form\Type\EditBotRequestFormType;
use Domain\Exchange\Entity\Bot;
use Domain\Exchange\UseCase\Request\CreateBotRequest;
use Domain\Exchange\UseCase\Request\EditBotRequest;
use Domain\Exchange\ValueObject\BotId;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/bots")
 * @Security("has_role('ROLE_CONTROL')")
 */
class BotsController extends Controller
{
	/**
	 * @Route("/", name="control.bots.list")
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
			$createRequest = $form->getData();
			$response = $this->get('UseCase\CreateBotUseCase')->execute($createRequest);
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
		$bot = $this->get('ORM\BotRepository')->findById(new BotId($id));
		$editRequest = new EditBotRequest();
		$editRequest->setBotId($bot->getId());
		$editRequest->setExchangeId($bot->getExchangeId());
		$editRequest->setTradingStrategyId($bot->getTradingStrategyId());
		$editRequest->setTradingStrategySettings($bot->getTradingStrategySettings());
		$editRequest->setStatus($bot->getStatus());

		$form = $this->createForm(EditBotRequestFormType::class, $editRequest);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			/** @var EditBotRequest $request */
			$editRequest = $form->getData();
			$response = $this->get('UseCase\EditBotUseCase')->execute($editRequest);
			$this->addFlash('info', 'Bot edited');
			return $this->redirectToRoute('control.bots.list');
		}

		return $this->render('@Control/Bots/edit.html.twig', [
			'bot' => $bot,
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/view/{id}", name="control.bots.view")
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @throws \Domain\Exception\EntityNotFoundException
	 */
	public function viewAction($id)
	{
		$bot = $this->get('ORM\BotRepository')->findById(new BotId($id));
		return $this->render('@Control/Bots/view.html.twig', [
			'bot' => $bot,
		]);
	}
}