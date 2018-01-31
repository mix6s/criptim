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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users")
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
	 * @Route("/{userId}/profileData", name="control.users.profileData")
	 * @param string $userId
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function userProfileDataAction(string $userId)
	{
		$userId = new UserId($userId);
		$balance = $deposits = $cashouts = $profitabilityAmount = new Money(0, new Currency('BTC'));
		$userExchangeAccounts = $this->get('ORM\UserExchangeAccountRepository')->findByUserId($userId);
		foreach ($userExchangeAccounts as $userExchangeAccount) {
			$balance = $balance->add($userExchangeAccount->getBalance());
		}
		$userExchangeAccountTransactionDeposits = $this->get('ORM\UserExchangeAccountTransactionRepository')->findByUserIdType($userId, 'deposit');
		foreach ($userExchangeAccountTransactionDeposits as $userExchangeAccountTransactionDeposit) {
			$deposits = $deposits->add($userExchangeAccountTransactionDeposit->getMoney());
		}
		$userExchangeAccountTransactionCashouts = $this->get('ORM\UserExchangeAccountTransactionRepository')->findByUserIdType($userId, 'cashout');
		foreach ($userExchangeAccountTransactionCashouts as $userExchangeAccountTransactionCashout) {
			$cashouts = $cashouts->add($userExchangeAccountTransactionCashout->getMoney());
		}
		$profitability = [
			'amount' => $profitabilityAmount,
			'percent' => 0
		];
		$context = [
			'balance' => $balance,
			'deposits' => $deposits,
			'cashouts' => $cashouts,
			'profitability' => $profitability
		];
		return $this->render('@Control/Users/profileData.html.twig', $context);

	}


}