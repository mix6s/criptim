<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/25/18
 * Time: 10:53 AM
 */

namespace ControlBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\Form\Type\RegistrationFormType;
use ControlBundle\Form\Type\UserDepositMoneyRequestFormType;
use Domain\Exchange\UseCase\Request\UserDepositMoneyRequest;
use Domain\Exchange\ValueObject\ExchangeId;
use Domain\ValueObject\UserId;
use FintobitBundle\Form\ChoosePeriodForm;
use FintobitBundle\Form\ChoosePeriodFormData;
use FintobitBundle\Form\Periods;
use Money\Currency;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Domain\UseCase\Request\CreateUserRequest;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @Route("/users")
 * @Security("has_role('ROLE_CONTROL')")
 */
class UsersController extends Controller
{
	/**
	 * @Route("/", name="control.users.list")
	 */
	public function listAction(Request $request)
	{
		/** @var User $users */
		$users = $this->get('fos_user.user_manager')->findUsers();
		$exchanges = $this->get('ExchangeRepository')->findAll();

		return $this->render('@Control/Users/list.html.twig', [
			'users' => $users,
			'exchanges' => $exchanges
		]);
	}

	/**
	 * @Route("/{userId}/deposit/{exchangeId}", name="control.users.exchangeDeposit")
	 */
	public function exchangeDepositAction(Request $request, $userId, $exchangeId)
	{
		$userDepositMoneyRequest = new UserDepositMoneyRequest();
		$userDepositMoneyRequest->setUserId(new UserId($userId));
		$userDepositMoneyRequest->setExchangeId(new ExchangeId($exchangeId));
		$userDepositMoneyRequest->setCurrency(new Currency('BTC'));

		$form = $this->createForm(UserDepositMoneyRequestFormType::class, $userDepositMoneyRequest);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			/** @var UserDepositMoneyRequest $request */
			$createRequest = $form->getData();
			$this->get('doctrine.orm.default_entity_manager')->transactional(function () use ($createRequest) {
				$response = $this->get('UseCase\UserDepositMoneyUseCase')->execute($createRequest);
			});
			$this->addFlash('info', 'Deposit successfully created');
			return $this->redirectToRoute('control.users.list');
		}

		return $this->render('@Control/Users/exchangeDeposit.html.twig', [
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/{userId}/profile", name="control.users.profile")
	 * @param Request $request
	 * @param $userId
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function profileAction(Request $request, $userId)
	{
		$userId = new UserId($userId);
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
			'balance' => $this->get('AdminProfileDataViewer')->getBalanceMoneyByUserId($userId),
			'deposits' => $this->get('AdminProfileDataViewer')->getDepositsMoneyByUserId($userId),
			'cashouts' => $this->get('AdminProfileDataViewer')->getCashoutsMoneyByUserId($userId),
			'fee' => $this->get('AdminProfileDataViewer')->getFeeMoneyByUserId($userId),
			'profitability' => $this->get('AdminProfileDataViewer')->getProfitabilityByUserId($userId),
			'transactions' => $this->get('AdminProfileDataViewer')->getTransactionHistory($userId),
			'form' => $choosePeriodForm->createView(),
			'currentPeriod' => $currentPeriod,
			'userId' => $userId
		];

		return $this->render('@Control/Users/index.html.twig', $context);
	}

	/**
	 * @Route("/{userId}/profile/period_data.json", name="control.users.profile.period_data")
	 */
	public function balanceHistoryAction(Request $request, $userId)
	{
		$userId = new UserId($userId);
		$period = $request->query->get('period');
		$periods = new Periods();
		[$fromDt, $toDt] = $periods->resolveDateRangeForPeriod($period);
		$balanceChangeDuringPeriodAggregate = $this->get('AdminProfileDataViewer')
			->getPeriodChangeProfileDataAggregateByUserIdFromDtToDt(
				$userId,
				$fromDt,
				$toDt
			);

		return $this->json($balanceChangeDuringPeriodAggregate);
	}

	/**
	 * @Route("/registration", name="control.users.registration")
	 * @param Request $request
	 */
	public function registrationAction(Request $request)
	{
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
					$url = $this->generateUrl('control.users.list');
					$response = new RedirectResponse($url);
				}
//				$dispatcher
//					->dispatch(
//						FOSUserEvents::REGISTRATION_COMPLETED,
//						new FilterUserResponseEvent($user, $request, $response)
//					);

				return $this->redirectToRoute('control.users.list');
			}

			$event = new FormEvent($form, $request);
			$dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

			if (null !== $response = $event->getResponse()) {
				return $response;
			}
		}

		return $this->render(
			'@Control/Users/registration.html.twig',
			[
				'form' => $form->createView(),
				'layout_title' => 'Регистрация'
			]
		);
	}
}
