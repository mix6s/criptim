<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Domain\Policy\DomainCurrenciesPolicy;
use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('@App/Default/index.html.twig', [
        ]);
    }

    /**
     * @Route("/profile", name="profile")
    */
    public function profileAction()
    {
	    $user = $this->getUser();
	    if (!$user instanceof UserInterface) {
		    return $this->redirectToRoute('login');
	    }
    	$context = [];
	    return $this->render('@App/Default/profile.html.twig', $context);
    }

	/**
	 * @Route("/balance_history.json", name="balance_history.json")
	 */
	public function balanceHistoryAction()
    {
	    $user = $this->getUser();
	    if (!$user instanceof UserInterface) {
		    return $this->redirectToRoute('login');
	    }
	    /** @var User $user */
	    $userId = $user->getDomainUserId();

	    $em = $this->getDoctrine()->getManager();
	    // todo: move to service
	    $query = <<<QUERY
WITH dates AS (
    SELECT generate_series(
               :from_dt :: TIMESTAMP,
               :to_dt :: TIMESTAMP,
               '1 day' :: INTERVAL
           ) AS date
), transactions AS (
    SELECT
      date,
      (SELECT balance
       FROM user_exchange_account_transaction t
       WHERE t.dt < date
             AND t.user_id = :user_id
             AND t.currency = :currency
       ORDER BY t.dt DESC
       LIMIT 1) AS balance
    FROM dates
) SELECT
    TO_CHAR(date, 'YYYY-MM-DD') AS date,
    COALESCE((balance ->> 'amount') :: FLOAT / (10 ^ :subunit), 0) as balance
  FROM transactions;
QUERY;
	    /** @var \Doctrine\DBAL\Statement $statement */
	    $statement = $this->getDoctrine()->getConnection()->prepare($query);
	    $fromDt = (new \DateTimeImmutable('now - 1 month'))->format('Y-m-d H:i:s');
	    $toDt = (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s');
	    $currency = new \Money\Currency('BTC');
	    $subunit = (new DomainCurrenciesPolicy())->subunitFor($currency);
	    $statement->bindParam('from_dt', $fromDt);
	    $statement->bindParam('to_dt', $toDt);
	    $statement->bindParam('user_id', $userId);
	    $statement->bindParam('subunit', $subunit);
	    $statement->bindParam('currency', $currency);
	    $statement->execute();
	    $result = $statement->fetchAll();

	    return $this->json($result);
    }

    /**
     * @Route("/main", name="main")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testAction(Request $request)
    {
        return $this->render('@App/Default/main.html.twig');
    }

    /**
     * @Route("/main_pro", name="main_pro")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mainProAction(Request $request)
    {
        return $this->render('@App/Default/main-pro.html.twig');
    }

    /**
     * @Route("/cashier", name="cashier")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cashierAction(Request $request)
    {
        return $this->render('@App/Default/cashier.html.twig');
    }

    /**
     * @Route("/cashier_complete", name="cashier_complete")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cashierCompleteAction(Request $request)
    {
        return $this->render('@App/Default/cashier-complete.html.twig');

    }

    /**
     * @Route("/cashier_error", name="cashier_error")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cashierErrorAction(Request $request)
    {
        return $this->render('@App/Default/cashier-error.html.twig');

    }

    /**
     * @Route("/cashier_recharge", name="cashier_recharge")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cashierRechargeAction(Request $request)
    {
        return $this->render('@App/Default/cashier-recharge.html.twig');

    }

    /**
     * @Route("/cashier_withdraw", name="cashier_withdraw")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cashierWithdrawAction(Request $request)
    {
        return $this->render('@App/Default/cashier-withdraw.html.twig');

    }

    /**
     * @Route("/cashier_authorization", name="cashier_authorization")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cashierAuthorizationAction(Request $request)
    {
        return $this->render('@App/Default/cashier_authorization.html.twig');

    }

    /**
     * @Route("/monitoring", name="monitoring")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function monitoringAction(Request $request)
    {
        return $this->render('@App/Default/monitoring.html.twig');
    }

    /**
     * @Route("/about", name="about")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function aboutAction(Request $request)
    {
        return $this->render('@App/Default/page-about.html.twig');
    }

    /**
     * @Route("/rules", name="rules")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rulesAction(Request $request)
    {
        return $this->render('@App/Default/rules.html.twig');
    }

    /**
     * @Route("/auth", name="auth")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function authAction(Request $request)
    {
        return $this->render('@App/Default/auth.html.twig');

    }

    /**
     * @Route("/popup_commission", name="popup_commission")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function popupCommissionAction()
    {
        return $this->render('@App/Default/popup-commission.html.twig');
    }

}
