<?php

namespace Fintobit\Controller;

use AppBundle\Entity\User;
use Domain\ValueObject\UserId;
use FOS\UserBundle\Model\UserInterface;
use Money\Currency;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class ProfileController
 * @package AppBundle\Controller
 */
class ProfileController extends Controller
{

	/**
	 * @Route("/profile", name="fintobit.profile.index")
	 */
	public function profileAction()
	{
		$user = $this->getUser();
		if (!$user instanceof UserInterface) {
			return $this->redirectToRoute('investor.login');
		}
		/** @var User $user */
		$userId = $user->getDomainUserId();
		if (!$userId instanceof UserId) {
			return $this->redirectToRoute('investor.login');
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
		return $this->render('@App/Investor/index.html.twig', $context);
	}

	/**
	 * @Route("/profile/history.json", name="fintobit.profile.history")
	 */
	public function balanceHistoryAction()
	{
		$user = $this->getUser();
		if (!$user instanceof UserInterface) {
			return $this->redirectToRoute('investor.login');
		}
		/** @var User $user */
		$userId = $user->getDomainUserId();
		if (!$userId instanceof UserId) {
			return $this->redirectToRoute('login');
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