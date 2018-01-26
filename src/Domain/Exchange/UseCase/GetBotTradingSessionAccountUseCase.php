<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/26/18
 * Time: 1:38 PM
 */

namespace Domain\Exchange\UseCase;


use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\BotTradingSessionAccount;
use Domain\Exchange\Repository\BotTradingSessionAccountRepositoryInterface;
use Domain\Exchange\UseCase\Request\GetBotTradingSessionAccountRequest;
use Domain\Exchange\UseCase\Response\GetBotTradingSessionAccountResponse;

class GetBotTradingSessionAccountUseCase
{
	/**
	 * @var BotTradingSessionAccountRepositoryInterface
	 */
	private $botTradingSessionAccountRepository;

	public function __construct(
		BotTradingSessionAccountRepositoryInterface $botTradingSessionAccountRepository
	)
	{
		$this->botTradingSessionAccountRepository = $botTradingSessionAccountRepository;
	}

	public function execute(GetBotTradingSessionAccountRequest $request): GetBotTradingSessionAccountResponse
	{
		try {
			$account = $this->botTradingSessionAccountRepository->findByBotTradingSessionIdCurrency($request->getBotTradingSessionId(), $request->getCurrency());
		} catch (EntityNotFoundException $exception) {
			$account = new BotTradingSessionAccount($request->getBotTradingSessionId(), $request->getCurrency());
			$this->botTradingSessionAccountRepository->save($account);
		}
		return new GetBotTradingSessionAccountResponse($account);
	}
}