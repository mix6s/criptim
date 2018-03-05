<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/26/18
 * Time: 7:00 PM
 */

namespace DomainBundle\Command;


use Domain\Entity\User;
use Domain\Entity\UserAccountTransaction;
use Domain\Exchange\Entity\Bot;
use Domain\Exchange\Entity\UserExchangeAccountTransaction;
use Domain\Exchange\UseCase\Request\ProcessBotTradingRequest;
use Domain\Exchange\UseCase\Request\SyncExchangeRequest;
use Domain\UseCase\Request\GetUserAccountRequest;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserAccountCommand extends ContainerAwareCommand
{

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$getUserAccountRequest = new GetUserAccountRequest();
		/** @var User[] $users */
		$users = $this->getContainer()->get('ORM\UserRepository')->findAll();
		foreach ($users as $user) {
			$transactions = $this->getContainer()->get('ORM\UserExchangeAccountTransactionRepository')->findBy([
				'userId' => $user->getId(),
			], [
				'dt' => 'ASC'
			]);
			/** @var UserExchangeAccountTransaction $transaction */
			foreach ($transactions as $transaction) {
				$getUserAccountRequest->setUserId($user->getId());
				$getUserAccountRequest->setCurrency($transaction->getCurrency());
				$account = $this->getContainer()->get('UseCase\GetUserAccountUseCase')->execute($getUserAccountRequest)->getUserAccount();

				$account->change($transaction->getMoney());
				$id = $this->getContainer()->get('Exchange\IdFactory')->getUserAccountTransactionId();
				$accTransaction = new UserAccountTransaction($id, $user->getId(), $transaction->getCurrency(),
					$transaction->getMoney(), $account->getBalance(), $transaction->getType(), $transaction->getDt());
				$this->getContainer()->get('ORM\UserAccountRepository')->save($account);
				$this->getContainer()->get('ORM\UserAccountTransactionRepository')->save($accTransaction);
			}
		}
	}

	protected function configure()
	{
		$this
			->setName('domain:user-account-transfer');
	}
}