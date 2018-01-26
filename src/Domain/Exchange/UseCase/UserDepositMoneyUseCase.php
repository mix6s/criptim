<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:26
 */

namespace Domain\Exchange\UseCase;


use Domain\Exception\DomainException;
use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\BotExchangeAccount;
use Domain\Exchange\Entity\BotExchangeAccountTransaction;
use Domain\Exchange\Entity\UserExchangeAccount;
use Domain\Exchange\Entity\UserExchangeAccountTransaction;
use Domain\Exchange\Factory\IdFactoryInterface;
use Domain\Exchange\Policy\MoneyFromFloatPolicy;
use Domain\Exchange\Policy\MoneyFromFloatPolicyInterface;
use Domain\Exchange\Repository\BotExchangeAccountRepositoryInterface;
use Domain\Exchange\Repository\BotExchangeAccountTransactionRepositoryInterface;
use Domain\Exchange\Repository\BotRepositoryInterface;
use Domain\Exchange\Repository\ExchangeAccountRepositoryInterface;
use Domain\Exchange\Repository\ExchangeRepositoryInterface;
use Domain\Exchange\Repository\UserExchangeAccountRepositoryInterface;
use Domain\Exchange\Repository\UserExchangeAccountTransactionRepositoryInterface;
use Domain\Exchange\UseCase\Request\GetBotExchangeAccountRequest;
use Domain\Exchange\UseCase\Request\GetUserExchangeAccountRequest;
use Domain\Exchange\UseCase\Request\UserDepositMoneyRequest;
use Domain\Exchange\UseCase\Response\UserDepositMoneyResponse;
use Domain\Repository\UserRepositoryInterface;
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
	 * @var UserExchangeAccountRepositoryInterface
	 */
	private $userExchangeAccountRepository;
	/**
	 * @var IdFactoryInterface
	 */
	private $idFactory;
	/**
	 * @var BotExchangeAccountRepositoryInterface
	 */
	private $botExchangeAccountRepository;
	/**
	 * @var UserExchangeAccountTransactionRepositoryInterface
	 */
	private $userExchangeAccountTransactionRepository;
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
	 * @var GetUserExchangeAccountUseCase
	 */
	private $getUserExchangeAccountUseCase;

	public function __construct(
		UserRepositoryInterface $userRepository,
		ExchangeRepositoryInterface $exchangeRepository,
		UserExchangeAccountRepositoryInterface $userExchangeAccountRepository,
		IdFactoryInterface $idFactory,
		BotExchangeAccountRepositoryInterface $botExchangeAccountRepository,
		UserExchangeAccountTransactionRepositoryInterface $userExchangeAccountTransactionRepository,
		BotExchangeAccountTransactionRepositoryInterface $botExchangeAccountTransactionRepository,
		BotRepositoryInterface $botRepository,
		GetBotExchangeAccountUseCase $getBotExchangeAccountUserCase,
		GetUserExchangeAccountUseCase $getUserExchangeAccountUseCase
	) {
		$this->userRepository = $userRepository;
		$this->exchangeRepository = $exchangeRepository;
		$this->userExchangeAccountRepository = $userExchangeAccountRepository;
		$this->idFactory = $idFactory;
		$this->botExchangeAccountRepository = $botExchangeAccountRepository;
		$this->userExchangeAccountTransactionRepository = $userExchangeAccountTransactionRepository;
		$this->botRepository = $botRepository;
		$this->botExchangeAccountTransactionRepository = $botExchangeAccountTransactionRepository;
		$this->moneyFromFloatPolicy = new MoneyFromFloatPolicy();
		$this->getBotExchangeAccountUserCase = $getBotExchangeAccountUserCase;
		$this->getUserExchangeAccountUseCase = $getUserExchangeAccountUseCase;
	}

	public function execute(UserDepositMoneyRequest $request): UserDepositMoneyResponse
	{
		$user = $this->userRepository->findById($request->getUserId());
		$exchange = $this->exchangeRepository->findById($request->getExchangeId());
		$money = $this->moneyFromFloatPolicy->getMoney($request->getCurrency(), $request->getAmount());

		$getUserExchangeAccountRequest = new GetUserExchangeAccountRequest();
		$getUserExchangeAccountRequest->setUserId($user->getId());
		$getUserExchangeAccountRequest->setCurrency($money->getCurrency());
		$getUserExchangeAccountRequest->setExchangeId($exchange->getId());
		$userAccount = $this->getUserExchangeAccountUseCase->execute($getUserExchangeAccountRequest)->getUserExchangeAccount();
		$userAccount->change($money);

		$transactionId = $this->idFactory->getUserExchangeAccountTransactionId();
		$transaction = new UserExchangeAccountTransaction(
			$transactionId,
			$user->getId(),
			$exchange->getId(),
			$money->getCurrency(),
			$money,
			$userAccount->getBalance(),
			UserExchangeAccountTransaction::TYPE_DEPOSIT
		);
		$this->userExchangeAccountTransactionRepository->save($transaction);
		$this->userExchangeAccountRepository->save($userAccount);
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