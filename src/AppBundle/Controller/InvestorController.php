<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\Type\RegistrationFormType;
use Domain\UseCase\Request\CreateUserRequest;
use Domain\ValueObject\UserId;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Money\Currency;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

/**
 * Class InvestorController
 * @package AppBundle\Controller
 */
class InvestorController extends Controller
{

    /**
     * @Route("/inv/info", name="investor.info")
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
	 * @Route("/inv/history.json", name="investor.history")
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

	/**
	 * @Route("/inv/registration", name="investor.registration")
	 */
	public function registrationAction(Request $request)
    {
	    if ($this->getUser()) {
		    return $this->redirectToRoute('investor.info');
	    }
	    /** @var $userManager UserManagerInterface */
	    $userManager = $this->get('fos_user.user_manager');
	    /** @var $dispatcher EventDispatcherInterface */
	    $dispatcher = $this->get('event_dispatcher');

	    /** @var User $user */
	    $user = $userManager->createUser();
	    $user->setEnabled(true);

	    $event = new GetResponseUserEvent($user, $request);
	    $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

	    if (null !== $event->getResponse()) {
		    return $event->getResponse();
	    }

	    $form = $this->createForm(RegistrationFormType::class, $user, [
		    'validation_groups' => ['AppRegistration', 'Default']
	    ]);
	    $form->handleRequest($request);

	    if ($form->isSubmitted()) {
		    if ($form->isValid()) {
			    $event = new FormEvent($form, $request);
			    $response = $this->get('UseCase\CreateUserUseCase')->execute(new CreateUserRequest());
			    $user->setDomainUserId($response->getUser()->getId());
			    $user->addRole(User::ROLE_INVESTOR);
			    $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
			    $userManager->updateUser($user);
			    $response = $event->getResponse();
			    if (null === $response) {
				    $url = $this->generateUrl('fos_user_registration_confirmed');
				    $response = new RedirectResponse($url);
			    }
			    $dispatcher
				    ->dispatch(
				    	FOSUserEvents::REGISTRATION_COMPLETED,
					    new FilterUserResponseEvent($user, $request, $response)
				    );

			    return $this->redirectToRoute('investor.info');
		    }

		    $event = new FormEvent($form, $request);
		    $dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

		    if (null !== $response = $event->getResponse()) {
			    return $response;
		    }
	    }

	    return $this->render(
	    	'@App/Investor/registration.hmtl.twig',
		    [
		    	'form' => $form->createView(),
    		    'layout_title' => 'Регистрация'
		    ]
	    );
    }

	/**
	 * @Route("/inv/login", name="investor.login")
	 */
	public function loginAction(Request $request)
    {
	    if ($this->getUser()) {
		    return $this->redirectToRoute('homepage');
	    }
	    /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
	    $session = $request->getSession();

	    $authErrorKey = Security::AUTHENTICATION_ERROR;
	    $lastUsernameKey = Security::LAST_USERNAME;

	    // get the error if any (works with forward and redirect -- see below)
	    if ($request->attributes->has($authErrorKey)) {
		    $error = $request->attributes->get($authErrorKey);
	    } elseif (null !== $session && $session->has($authErrorKey)) {
		    $error = $session->get($authErrorKey);
		    $session->remove($authErrorKey);
	    } else {
		    $error = null;
	    }

	    if (!$error instanceof AuthenticationException) {
		    $error = null; // The value does not come from the security component.
	    }

	    // last username entered by the user
	    $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

	    $csrfToken = $this->has('security.csrf.token_manager')
		    ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
		    : null;
	    return $this->render('@App/Investor/login.hmtl.twig', [
		    'last_username' => $lastUsername,
		    'error' => $error,
		    'csrf_token' => $csrfToken,
		    'layout_title' => 'Авторизация'
	    ]);
    }
}
