<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/26/18
 * Time: 1:38 PM
 */

namespace Domain\Exchange\UseCase;


use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\BotExchangeAccount;
use Domain\Exchange\Repository\BotExchangeAccountRepositoryInterface;
use Domain\Exchange\UseCase\Request\GetBotExchangeAccountRequest;
use Domain\Exchange\UseCase\Response\GetBotExchangeAccountResponse;

class GetBotExchangeAccountUseCase
{
	/**
	 * @var BotExchangeAccountRepositoryInterface
	 */
	private $botExchangeAccountRepository;

	public function __construct(
		BotExchangeAccountRepositoryInterface $botExchangeAccountRepository
	)
	{
		$this->botExchangeAccountRepository = $botExchangeAccountRepository;
	}

	public function execute(GetBotExchangeAccountRequest $request): GetBotExchangeAccountResponse
	{
		try {
			$botAccount = $this->botExchangeAccountRepository->findByBotIdExchangeIdCurrency($request->getBotId(), $request->getExchangeId(), $request->getCurrency());
		} catch (EntityNotFoundException $exception) {
			$botAccount = new BotExchangeAccount($request->getBotId(), $request->getExchangeId(), $request->getCurrency());
			$this->botExchangeAccountRepository->save($botAccount);
		}
		return new GetBotExchangeAccountResponse($botAccount);
	}
}