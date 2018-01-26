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
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessTradingCommand extends ContainerAwareCommand
{
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$useCase = $this->getContainer()->get('UseCase\ProcessBotTradingUseCase');
		$em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

		/** @var Bot[] $bots */
		$bots = $this->getContainer()->get('ORM\BotRepository')->findAll();
		foreach ($bots as $bot) {
			if (!$bot->isActive()) {
				continue;
			}
			$botId = $bot->getId();

			$em->transactional(function () use ($useCase, $botId) {
				$processRequest = new ProcessBotTradingRequest($botId);
				$useCase->execute($processRequest);
			});
		}


	}

	protected function configure()
	{
		$this
			->setName('domain:process-trading');
	}
}