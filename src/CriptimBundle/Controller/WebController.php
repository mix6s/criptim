<?php

namespace CriptimBundle\Controller;

use AppBundle\Entity\User;
use Domain\ValueObject\UserId;
use FOS\UserBundle\Model\UserInterface;
use Money\Currency;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class WebController
 * @package AppBundle\Controller
 */
class WebController extends Controller
{
    /**
     * @Route("/", name="criptim.index")
     */
    public function indexAction(Request $request)
    {
        return $this->render('@Criptim/Web/index.html.twig', []);
    }

    /**
     * @Route("/profile", name="criptim.profile")
    */
    public function profileAction()
    {
	    $user = $this->getUser();
	    if (!$user instanceof UserInterface) {
		    return $this->redirectToRoute('criptim.auth.login');
	    }
    	$context = [];
	    return $this->render('@Criptim/Web/profile.html.twig', $context);
    }

	/**
	 * @Route("/balance_history.json", name="criptim.profile.history")
	 */
	public function balanceHistoryAction()
    {
	    $user = $this->getUser();
	    if (!$user instanceof UserInterface) {
		    return $this->redirectToRoute('criptim.auth.login');
	    }
	    /** @var User $user */
	    $userId = $user->getDomainUserId();
	    if (!$userId instanceof UserId) {
		    return $this->redirectToRoute('criptim.auth.login');
	    }
	    $fromDt = new \DateTimeImmutable('now - 1 month');
	    $toDt = new \DateTimeImmutable('now');
	    $currency = new Currency('BTC');
	    $result = $this->get('PublicBalanceHistory')->fetchByUserIdCurrencyFromDtToDt(
	    	$userId, $currency, $fromDt, $toDt
	    );
	    return $this->json($result);
    }

    /**
     * @Route("/main", name="main")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testAction(Request $request)
    {
        return $this->render('@Criptim/Web/main.html.twig');
    }

    /**
     * @Route("/main_pro", name="main_pro")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mainProAction(Request $request)
    {
        return $this->render('@Criptim/Web/main-pro.html.twig');
    }

    /**
     * @Route("/cashier", name="cashier")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cashierAction(Request $request)
    {
        return $this->render('@Criptim/Web/cashier.html.twig');
    }

    /**
     * @Route("/cashier_complete", name="cashier_complete")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cashierCompleteAction(Request $request)
    {
        return $this->render('@Criptim/Web/cashier-complete.html.twig');

    }

    /**
     * @Route("/cashier_error", name="cashier_error")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cashierErrorAction(Request $request)
    {
        return $this->render('@Criptim/Web/cashier-error.html.twig');

    }

    /**
     * @Route("/cashier_recharge", name="cashier_recharge")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cashierRechargeAction(Request $request)
    {
        return $this->render('@Criptim/Web/cashier-recharge.html.twig');

    }

    /**
     * @Route("/cashier_withdraw", name="cashier_withdraw")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cashierWithdrawAction(Request $request)
    {
        return $this->render('@Criptim/Web/cashier-withdraw.html.twig');

    }

    /**
     * @Route("/cashier_authorization", name="cashier_authorization")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cashierAuthorizationAction(Request $request)
    {
        return $this->render('@Criptim/Web/cashier_authorization.html.twig');

    }

    /**
     * @Route("/monitoring", name="monitoring")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function monitoringAction(Request $request)
    {
        return $this->render('@Criptim/Web/monitoring.html.twig');
    }

    /**
     * @Route("/about", name="about")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function aboutAction(Request $request)
    {
        return $this->render('@Criptim/Web/page-about.html.twig');
    }

    /**
     * @Route("/rules", name="rules")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rulesAction(Request $request)
    {
        return $this->render('@Criptim/Web/rules.html.twig');
    }

    /**
     * @Route("/auth", name="auth")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function authAction(Request $request)
    {
        return $this->render('@Criptim/Web/auth.html.twig');

    }

    /**
     * @Route("/popup_commission", name="popup_commission")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function popupCommissionAction()
    {
        return $this->render('@Criptim/Web/popup-commission.html.twig');
    }

}
