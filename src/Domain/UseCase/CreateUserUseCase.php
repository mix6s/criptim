<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 17:18
 */

namespace Domain\UseCase;


use Domain\Entity\User;
use Domain\Factory\UserIdFactoryInterface;
use Domain\Repository\UserRepositoryInterface;
use Domain\UseCase\Request\CreateUserRequest;
use Domain\UseCase\Response\CreateUserResponse;

class CreateUserUseCase
{
	/**
	 * @var UserIdFactoryInterface
	 */
	private $userIdFactory;
	/**
	 * @var UserRepositoryInterface
	 */
	private $userRepository;

	public function __construct(
		UserIdFactoryInterface $userIdFactory,
		UserRepositoryInterface $userRepository
	)
	{
		$this->userIdFactory = $userIdFactory;
		$this->userRepository = $userRepository;
	}

	public function execute(CreateUserRequest $request): CreateUserResponse
	{
		$id = $this->userIdFactory->getUserId();
		$user = new User($id);
		$this->userRepository->save($user);
		return new CreateUserResponse($id);
	}
}