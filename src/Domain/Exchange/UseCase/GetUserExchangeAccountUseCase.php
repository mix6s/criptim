<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/26/18
 * Time: 1:38 PM
 */

namespace Domain\Exchange\UseCase;


use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\UserExchangeAccount;
use Domain\Exchange\Repository\UserExchangeAccountRepositoryInterface;
use Domain\Exchange\UseCase\Request\GetUserExchangeAccountRequest;
use Domain\Exchange\UseCase\Response\GetUserExchangeAccountResponse;

class GetUserExchangeAccountUseCase
{
	/**
	 * @var UserExchangeAccountRepositoryInterface
	 */
	private $userExchangeAccountRepository;

	public function __construct(
		UserExchangeAccountRepositoryInterface $userExchangeAccountRepository
	)
	{
		$this->userExchangeAccountRepository = $userExchangeAccountRepository;
	}

	public function execute(GetUserExchangeAccountRequest $request): GetUserExchangeAccountResponse
	{
		try {
			$account = $this->userExchangeAccountRepository->findByUserIdExchangeIdCurrency($request->getUserId(), $request->getExchangeId(), $request->getCurrency());
		} catch (EntityNotFoundException $exception) {
			$account = new UserExchangeAccount($request->getUserId(), $request->getExchangeId(), $request->getCurrency());
			$this->userExchangeAccountRepository->save($account);
		}
		return new GetUserExchangeAccountResponse($account);
	}
}