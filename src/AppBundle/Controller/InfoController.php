<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Domain\Policy\DomainCurrenciesPolicy;
use Domain\ValueObject\UserId;
use FOS\UserBundle\Model\UserInterface;
use Money\Currency;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class InfoController
 * @package AppBundle\Controller
 */
class InfoController extends Controller
{

    /**
     * @Route("/info", name="info.info")
    */
    public function profileAction()
    {
	    $user = $this->getUser();
	    if (!$user instanceof UserInterface) {
		    return $this->redirectToRoute('login');
	    }
	    /** @var User $user */
	    $userId = $user->getDomainUserId();
	    if (!$userId instanceof UserId) {
	    	return $this->redirectToRoute('login');
	    }
	    $fromDate = new \DateTimeImmutable('this month');
	    $toDate = new \DateTimeImmutable('now');

	    $context = [
		    'balance' => $this->get('ProfileData')->getBalanceMoneyByUserId($userId),
		    'deposits' => $this->get('ProfileData')->getDepositsMoneyByUserId($userId),
		    'cashouts' => $this->get('ProfileData')->getCashoutsMoneyByUserId($userId),
		    'profitability' => $this->get('ProfitabilityCalculator')->getProfitabilityByUserIdFromDtToDt($userId, $fromDate, $toDate)
	    ];
	    return $this->render('@App/Info/index.html.twig', $context);
    }

	/**
	 * @Route("/info/history.json", name="info.history")
	 */
	public function balanceHistoryAction()
    {
	    $user = $this->getUser();
	    if (!$user instanceof UserInterface) {
		    return $this->redirectToRoute('login');
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
