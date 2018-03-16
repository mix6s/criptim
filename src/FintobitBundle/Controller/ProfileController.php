<?php

namespace FintobitBundle\Controller;

use AppBundle\Entity\User;
use FintobitBundle\Form\ChoosePeriodForm;
use FintobitBundle\Form\ChoosePeriodFormData;
use FintobitBundle\Form\Periods;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
	public function profileAction(Request $request)
	{
		/** @var User $user */
		$user = $this->getUser();
		$userId = $user->getDomainUserId();
		if ($userId === null) {
			throw $this->createNotFoundException();
		}
		$choosePeriodFormData = new ChoosePeriodFormData();
		$now = new \DateTimeImmutable('now');
		$periods = new Periods();
		$currentPeriod = $periods->resolvePeriodForDateTime($now);
		$choosePeriodFormData->setPeriod($currentPeriod);
		$choosePeriodForm = $this->createForm(
			ChoosePeriodForm::class,
			$choosePeriodFormData
		);

		$choosePeriodForm->handleRequest($request);
		$context = [
			'balance' => $this->get('ProfileData')->getBalanceMoneyByUserId($userId),
			'deposits' => $this->get('ProfileData')->getDepositsMoneyByUserId($userId),
			'cashouts' => $this->get('ProfileData')->getCashoutsMoneyByUserId($userId),
			'fee' => $this->get('ProfileData')->getFeeMoneyByUserId($userId),
			'profitability' => $this->get('ProfileData')->getProfitabilityByUserId($userId),
			'transactions' => $this->get('ProfileData')->getTransactionHistory($userId),
			'form' => $choosePeriodForm->createView(),
			'currentPeriod' => $currentPeriod
		];
		return $this->render('@Fintobit/Profile/index.html.twig', $context);
	}

	/**
	 * @Route("/profile/period_data.json", name="fintobit.profile.period_data")
	 * @param Request $request
	 * @return JsonResponse
	 * @throws \Exception
	 */
	public function balanceChangeDuringPeriodAction(Request $request): JsonResponse
	{
		/** @var User $user */
		$user = $this->getUser();
		$userId = $user->getDomainUserId();
		if ($userId === null) {
			throw $this->createNotFoundException();
		}
		$period = $request->query->get('period');
		$periods = new Periods();
		[$fromDt, $toDt] = $periods->resolveDateRangeForPeriod($period);
		$balanceChangeDuringPeriodAggregate = $this->get('ProfileData')
			->getPeriodChangeProfileDataAggregateByUserIdFromDtToDt(
				$userId,
				$fromDt,
				$toDt
			);

		return $this->json($balanceChangeDuringPeriodAggregate);
	}

}