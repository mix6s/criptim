<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 12.01.2018
 * Time: 23:51
 */

namespace Domain\UseCase;


class ProcessBotTradingUseCase
{
	/**
	 * @var BotRepositoryInterface
	 */
	private $botRepository;

	public function __construct(
		BotRepositoryInterface $botRepository,
		BotStrategyRepositoryInterface $botStrategyRepository
	)
	{
		$this->botRepository = $botRepository;
	}

	public function execute(ProcessBotTradingRequest $request)
	{
		$bots = $this->botRepository->findEnabledBots();
		foreach ($bots as $bot) {
			$this->
		}
	}
}