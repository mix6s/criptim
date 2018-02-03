<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 20.06.2017
 * Time: 8:53
 */

namespace AppBundle\EventListener;


use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class AfterRegistrationListener
 * @package AppBundle\EventListner
 */
class AfterRegistrationListener implements EventSubscriberInterface
{
	private $router;
	/**
	 * @var string
	 */
	private $defaultRoute;

	public function __construct(UrlGeneratorInterface $router, string $defaultRoute = 'homepage')
	{
		$this->router = $router;
		$this->defaultRoute = $defaultRoute;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return [
			FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
		];
	}

	/**
	 * @param FormEvent $event
	 */
	public function onRegistrationSuccess(FormEvent $event)
	{
		$url = $event->getRequest()->getSession()->get('_security.main.target_path');
		if (empty($url)) {
			$url = $this->router->generate($this->defaultRoute);
		} else {
			$event->getRequest()->getSession()->remove('_security.main.target_path');
		}
		$event->setResponse(new RedirectResponse($url));
	}
}
