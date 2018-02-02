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
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessTradingCommand extends ContainerAwareCommand
{
	use LockableTrait;

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		if (!$this->lock()) {
			return 0;
		}


		$useCase = $this->getContainer()->get('UseCase\ProcessBotTradingUseCase');
		$syncUseCase = $this->getContainer()->get('UseCase\SyncExchangeUseCase');
		$syncRequest = new SyncExchangeRequest();

		while (true) {
			try {
				$em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
				/** @var Bot[] $bots */
				$bots = $this->getContainer()->get('ORM\BotRepository')->findAll();
				foreach ($bots as $bot) {
					$botId = $bot->getId();

					$em->transactional(function () use ($syncUseCase, $bot, $syncRequest) {
						$syncRequest->setExchangeId($bot->getExchangeId());
						$syncUseCase->execute($syncRequest);
					});
					$em->clear();
					$em->transactional(function () use ($useCase, $botId) {
						$processRequest = new ProcessBotTradingRequest($botId);
						$useCase->execute($processRequest);
					});
					$em->clear();
				}
			} catch (\Throwable $exception) {
				$this->release();
				throw $exception;
			}
			sleep(1);
		}

		$this->release();

	}

	protected function configure()
	{
		$this
			->setName('domain:process-trading');
	}
}