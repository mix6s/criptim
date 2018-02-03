<?php

namespace FintobitBundle\Controller;

use AppBundle\Entity\User;
use Domain\ValueObject\UserId;
use FOS\UserBundle\Model\UserInterface;
use Money\Currency;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class ProfileController
 * @package Fintobit\Controller
 * @Security("has_role('ROLE_INVESTOR')")
 */
class ProfileController extends Controller
{

	/**
	 * @Route("/profile", name="fintobit.profile.index")
	 */
	public function profileAction()
	{
		/** @var User $user */
		$user = $this->getUser();
		$userId = $user->getDomainUserId();
		if ($userId === null) {
			throw $this->createNotFoundException();
		}
		$fromDate = new \DateTimeImmutable('now - 1 month');
		$toDate = new \DateTimeImmutable('now');
		$currency = new Currency('BTC');
		$result = $this->get('BalanceHistory')->fetchByUserIdCurrencyFromDtToDt(
			$userId, $currency, $fromDate, $toDate
		);
		$context = [
			'balance' => $this->get('ProfileData')->getBalanceMoneyByUserId($userId),
			'deposits' => $this->get('ProfileData')->getDepositsMoneyByUserId($userId),
			'cashouts' => $this->get('ProfileData')->getCashoutsMoneyByUserId($userId),
			'profitability' => $this->get('ProfitabilityCalculator')->getProfitabilityByUserIdFromDtToDt($userId, $fromDate, $toDate),
			'history' => json_encode($result),
			'layout_title' => 'Профиль пользователя'
		];
		return $this->render('@Fintobit/Profile/index.html.twig', $context);
	}

	/**
	 * @Route("/profile/history.json", name="fintobit.profile.history")
	 */
	public function balanceHistoryAction()
	{
		/** @var User $user */
		$user = $this->getUser();
		$userId = $user->getDomainUserId();
		if ($userId === null) {
			throw $this->createNotFoundException();
		}
		$fromDt = new \DateTimeImmutable('now - 1 month');
		$toDt = new \DateTimeImmutable('now');
		$currency = new Currency('BTC');
		$result = $this->get('BalanceHistory')->fetchByUserIdCurrencyFromDtToDt(
			$userId, $currency, $fromDt, $toDt
		);
		return $this->json($result);
	}

}