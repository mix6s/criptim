<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/26/18
 * Time: 7:00 PM
 */

namespace DomainBundle\Command;


use Domain\Exchange\Entity\Bot;
use Domain\Exchange\UseCase\Request\ProcessBotTradingRequest;
use Domain\Exchange\UseCase\Request\SyncExchangeRequest;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncCommand extends ContainerAwareCommand
{
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$useCase = $this->getContainer()->get('UseCase\SyncExchangeUseCase');
		$syncRequest = new SyncExchangeRequest();
		$em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

		$exchanges = $this->getContainer()->get('ExchangeRepository')->findAll();

		foreach ($exchanges as $exchange) {
			$em->transactional(function () use ($useCase, $exchange, $syncRequest) {
				$syncRequest->setExchangeId($exchange->getId());
				$useCase->execute($syncRequest);
			});
		}


	}

	protected function configure()
	{
		$this
			->setName('domain:sync-exchanges');
	}
}