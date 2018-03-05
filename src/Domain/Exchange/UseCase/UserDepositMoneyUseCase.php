<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:26
 */

namespace Domain\Exchange\UseCase;


use Domain\Entity\UserAccountTransaction;
use Domain\Exchange\Entity\BotExchangeAccountTransaction;
use Domain\Exchange\Entity\UserExchangeAccountTransaction;
use Domain\Exchange\Factory\IdFactoryInterface;
use Domain\Exchange\Policy\MoneyFromFloatPolicy;
use Domain\Exchange\Policy\MoneyFromFloatPolicyInterface;
use Domain\Exchange\Repository\BotExchangeAccountRepositoryInterface;
use Domain\Exchange\Repository\BotExchangeAccountTransactionRepositoryInterface;
use Domain\Exchange\Repository\BotRepositoryInterface;
use Domain\Exchange\Repository\ExchangeRepositoryInterface;
use Domain\Exchange\UseCase\Request\GetBotExchangeAccountRequest;
use Domain\Exchange\UseCase\Request\UserDepositMoneyRequest;
use Domain\Exchange\UseCase\Response\UserDepositMoneyResponse;
use Domain\Repository\UserAccountRepositoryInterface;
use Domain\Repository\UserAccountTransactionRepositoryInterface;
use Domain\Repository\UserRepositoryInterface;
use Domain\UseCase\GetUserAccountUseCase;
use Domain\UseCase\Request\GetUserAccountRequest;
use Money\Money;

class UserDepositMoneyUseCase
{
	/**
	 * @var UserRepositoryInterface
	 */
	private $userRepository;
	/**
	 * @var ExchangeRepositoryInterface
	 */
	private $exchangeRepository;
	/**
	 * @var UserAccountRepositoryInterface
	 */
	private $userAccountRepository;
	/**
	 * @var IdFactoryInterface
	 */
	private $idFactory;
	/**
	 * @var BotExchangeAccountRepositoryInterface
	 */
	private $botExchangeAccountRepository;
	/**
	 * @var UserAccountTransactionRepositoryInterface
	 */
	private $userAccountTransactionRepository;
	/**
	 * @var BotRepositoryInterface
	 */
	private $botRepository;
	/**
	 * @var BotExchangeAccountTransactionRepositoryInterface
	 */
	private $botExchangeAccountTransactionRepository;
	/**
	 * @var MoneyFromFloatPolicyInterface
	 */
	private $moneyFromFloatPolicy;
	/**
	 * @var GetBotExchangeAccountUseCase
	 */
	private $getBotExchangeAccountUserCase;
	/**
	 * @var GetUserAccountUseCase
	 */
	private $getUserAccountUseCase;

	public function __construct(
		UserRepositoryInterface $userRepository,
		ExchangeRepositoryInterface $exchangeRepository,
		UserAccountRepositoryInterface $userAccountRepository,
		IdFactoryInterface $idFactory,
		BotExchangeAccountRepositoryInterface $botExchangeAccountRepository,
		UserAccountTransactionRepositoryInterface $userAccountTransactionRepository,
		BotExchangeAccountTransactionRepositoryInterface $botExchangeAccountTransactionRepository,
		BotRepositoryInterface $botRepository,
		GetBotExchangeAccountUseCase $getBotExchangeAccountUserCase,
		GetUserAccountUseCase $getUserAccountUseCase
	) {
		$this->userRepository = $userRepository;
		$this->exchangeRepository = $exchangeRepository;
		$this->userAccountRepository = $userAccountRepository;
		$this->idFactory = $idFactory;
		$this->botExchangeAccountRepository = $botExchangeAccountRepository;
		$this->userAccountTransactionRepository = $userAccountTransactionRepository;
		$this->botRepository = $botRepository;
		$this->botExchangeAccountTransactionRepository = $botExchangeAccountTransactionRepository;
		$this->moneyFromFloatPolicy = new MoneyFromFloatPolicy();
		$this->getBotExchangeAccountUserCase = $getBotExchangeAccountUserCase;
		$this->getUserAccountUseCase = $getUserAccountUseCase;
	}

	public function execute(UserDepositMoneyRequest $request): UserDepositMoneyResponse
	{
		$user = $this->userRepository->findById($request->getUserId());
		$exchange = $this->exchangeRepository->findById($request->getExchangeId());
		$money = $this->moneyFromFloatPolicy->getMoney($request->getCurrency(), $request->getAmount());

		$getUserExchangeAccountRequest = new GetUserAccountRequest();
		$getUserExchangeAccountRequest->setUserId($user->getId());
		$getUserExchangeAccountRequest->setCurrency($money->getCurrency());
		$userAccount = $this->getUserAccountUseCase->execute($getUserExchangeAccountRequest)->getUserAccount();
		$userAccount->change($money);

		$transactionId = $this->idFactory->getUserAccountTransactionId();
		$transaction = new UserAccountTransaction(
			$transactionId,
			$user->getId(),
			$money->getCurrency(),
			$money,
			$userAccount->getBalance(),
			UserAccountTransaction::TYPE_DEPOSIT
		);
		$this->userAccountTransactionRepository->save($transaction);
		$this->userAccountRepository->save($userAccount);
		$bots = $this->botRepository->findByExchangeId($exchange->getId());
		$botMoney = $money->divide(count($bots), Money::ROUND_DOWN);
		foreach ($bots as $bot) {
			$getBotExchangeAccountRequest = new GetBotExchangeAccountRequest();
			$getBotExchangeAccountRequest->setBotId($bot->getId());
			$getBotExchangeAccountRequest->setExchangeId($exchange->getId());
			$getBotExchangeAccountRequest->setCurrency($money->getCurrency());
			$botAccount = $this->getBotExchangeAccountUserCase->execute($getBotExchangeAccountRequest)->getBotExchangeAccount();
			$botAccount->change($botMoney);
			$botTransactionId = $this->idFactory->getBotExchangeAccountTransactionId();
			$botTransaction = new BotExchangeAccountTransaction(
				$botTransactionId,
				$bot->getId(),
				$exchange->getId(),
				$botMoney->getCurrency(),
				$botMoney,
				$botAccount->getBalance(),
				BotExchangeAccountTransaction::TYPE_DEPOSIT
			);
			$this->botExchangeAccountTransactionRepository->save($botTransaction);
			$this->botExchangeAccountRepository->save($botAccount);
		}

		return new UserDepositMoneyResponse();
	}
}