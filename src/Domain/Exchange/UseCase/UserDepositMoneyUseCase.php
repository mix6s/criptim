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
use Domain\Exchange\Policy\MoneyFromFloatPolicyInterface;
use Domain\Exchange\Repository\BotExchangeAccountRepositoryInterface;
use Domain\Exchange\Repository\BotExchangeAccountTransactionRepositoryInterface;
use Domain\Exchange\Repository\BotRepositoryInterface;
use Domain\Exchange\Repository\ExchangeAccountRepositoryInterface;
use Domain\Exchange\Repository\ExchangeRepositoryInterface;
use Domain\Exchange\Repository\UserExchangeAccountRepositoryInterface;
use Domain\Exchange\Repository\UserExchangeAccountTransactionRepositoryInterface;
use Domain\Exchange\UseCase\Request\UserDepositMoneyRequest;
use Domain\Exchange\UseCase\Response\UserDepositMoneyResponse;
use Domain\Repository\UserRepositoryInterface;

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

	public function __construct(
		UserRepositoryInterface $userRepository,
		ExchangeRepositoryInterface $exchangeRepository,
		UserExchangeAccountRepositoryInterface $userExchangeAccountRepository,
		IdFactoryInterface $idFactory,
		BotExchangeAccountRepositoryInterface $botExchangeAccountRepository,
		UserExchangeAccountTransactionRepositoryInterface $userExchangeAccountTransactionRepository,
		BotExchangeAccountTransactionRepositoryInterface $botExchangeAccountTransactionRepository,
		BotRepositoryInterface $botRepository,
		MoneyFromFloatPolicyInterface $moneyFromFloatPolicy
	) {
		$this->userRepository = $userRepository;
		$this->exchangeRepository = $exchangeRepository;
		$this->userExchangeAccountRepository = $userExchangeAccountRepository;
		$this->idFactory = $idFactory;
		$this->botExchangeAccountRepository = $botExchangeAccountRepository;
		$this->userExchangeAccountTransactionRepository = $userExchangeAccountTransactionRepository;
		$this->botRepository = $botRepository;
		$this->botExchangeAccountTransactionRepository = $botExchangeAccountTransactionRepository;
		$this->moneyFromFloatPolicy = $moneyFromFloatPolicy;
	}

	public function execute(UserDepositMoneyRequest $request): UserDepositMoneyResponse
	{
		$user = $this->userRepository->findById($request->getUserId());
		$exchange = $this->exchangeRepository->findById($request->getExchangeId());
		$money = $this->moneyFromFloatPolicy->getMoney($request->getCurrency(), $request->getAmount());

		try {
			$userAccount = $this->userExchangeAccountRepository->findByUserIdExchangeIdCurrency($user->getId(), $exchange->getId(), $money->getCurrency());
		} catch (EntityNotFoundException $exception) {
			$userAccount = new UserExchangeAccount($user->getId(), $exchange->getId(), $money->getCurrency());
		}

		$transactionId = $this->idFactory->getUserExchangeAccountTransactionId();
		$userAccount->change($money);
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
		$botMoney = $money->divide(count($bots));
		foreach ($bots as $bot) {
			try {
				$botAccount = $this->botExchangeAccountRepository->findByBotIdExchangeIdCurrency($bot->getId(), $exchange->getId(), $money->getCurrency());
			} catch (DomainException $exception) {
				$botAccount = new BotExchangeAccount($bot->getId(), $exchange->getId(), $money->getCurrency());
			}
			$botTransactionId = $this->idFactory->getBotExchangeAccountTransactionId();
			$botAccount->change($money);
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