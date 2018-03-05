<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/26/18
 * Time: 1:38 PM
 */

namespace Domain\UseCase;


use Domain\Exception\EntityNotFoundException;
use Domain\Entity\UserAccount;
use Domain\Repository\UserAccountRepositoryInterface;
use Domain\UseCase\Request\GetUserAccountRequest;
use Domain\UseCase\Response\GetUserAccountResponse;

class GetUserAccountUseCase
{
	/**
	 * @var UserAccountRepositoryInterface
	 */
	private $userAccountRepository;

	public function __construct(
		UserAccountRepositoryInterface $userAccountRepository
	)
	{
		$this->userAccountRepository = $userAccountRepository;
	}

	public function execute(GetUserAccountRequest $request): GetUserAccountResponse
	{
		try {
			$account = $this->userAccountRepository->findByUserIdCurrency($request->getUserId(), $request->getCurrency());
		} catch (EntityNotFoundException $exception) {
			$account = new UserAccount($request->getUserId(), $request->getCurrency());
			$this->userAccountRepository->save($account);
		}
		return new GetUserAccountResponse($account);
	}
}