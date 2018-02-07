<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/25/18
 * Time: 10:53 AM
 */

namespace ControlBundle\Controller;


use AppBundle\Entity\User;
use ControlBundle\Form\Type\UserDepositMoneyRequestFormType;
use Domain\Exchange\UseCase\Request\UserDepositMoneyRequest;
use Domain\Exchange\ValueObject\ExchangeId;
use Domain\ValueObject\UserId;
use Money\Currency;
use Money\Money;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users")
 * @Security("has_role('ROLE_CONTROL')")
 */
class UsersController extends Controller
{
	/**
	 * @Route("/", name="control.users.list")
	 */
	public function listAction(Request $request)
	{
		/** @var User $users */
		$users = $this->get('fos_user.user_manager')->findUsers();
		$exchanges = $this->get('ExchangeRepository')->findAll();

		return $this->render('@Control/Users/list.html.twig', [
			'users' => $users,
			'exchanges' => $exchanges
		]);
	}

	/**
	 * @Route("/{userId}/deposit/{exchangeId}", name="control.users.exchangeDeposit")
	 */
	public function exchangeDepositAction(Request $request, $userId, $exchangeId)
	{
		$userDepositMoneyRequest = new UserDepositMoneyRequest();
		$userDepositMoneyRequest->setUserId(new UserId($userId));
		$userDepositMoneyRequest->setExchangeId(new ExchangeId($exchangeId));
		$userDepositMoneyRequest->setCurrency(new Currency('BTC'));

		$form = $this->createForm(UserDepositMoneyRequestFormType::class, $userDepositMoneyRequest);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			/** @var UserDepositMoneyRequest $request */
			$createRequest = $form->getData();
			$this->get('doctrine.orm.default_entity_manager')->transactional(function () use ($createRequest) {
				$response = $this->get('UseCase\UserDepositMoneyUseCase')->execute($createRequest);
			});
			$this->addFlash('info', 'Deposit successfully created');
			return $this->redirectToRoute('control.users.list');
		}

		return $this->render('@Control/Users/exchangeDeposit.html.twig', [
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/{userId}/profile", name="control.users.profile")
	 */
	public function profileAction($userId)
	{
		$userId = new UserId($userId);
		$fromDate = new \DateTimeImmutable('now - 1 month');
		$toDate = new \DateTimeImmutable('now');
		$currency = new Currency('BTC');
		$result = $this->get('BalanceHistory')->fetchByUserIdCurrencyFromDtToDt(
			$userId, $currency, $fromDate, $toDate
		);
		$context = [
			'balance' => $this->get('ProfileData')->getBalanceMoneyByUserId($userId),
			'deposits' => $this->get('ProfileData')->getDepositsMoneyByUserId($userId),
			'cashouts' => $this->get('ProfileData')->getCashoutsMoneyByUserId($userId),
			'profitability' => $this->get('ProfitabilityCalculator')->getProfitabilityByUserIdFromDtToDt($userId, $currency, $fromDate, $toDate),
			'history' => json_encode($result),
			'userId' => $userId
		];
		return $this->render('@Control/Users/profile.html.twig', $context);
	}

	/**
	 * @Route("/{userId}/profile/history.json", name="control.users.profile.history")
	 */
	public function balanceHistoryAction($userId)
	{
		$fromDt = new \DateTimeImmutable('now - 1 month');
		$toDt = new \DateTimeImmutable('now');
		$currency = new Currency('BTC');
		$result = $this->get('BalanceHistory')->fetchByUserIdCurrencyFromDtToDt(
			new UserId($userId), $currency, $fromDt, $toDt
		);
		return $this->json($result);
	}
}
