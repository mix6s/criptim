<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 03.07.2017
 * Time: 10:22
 */

namespace AppBundle;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/**
 * Class LoginEntryPoint
 * @package AppBundle
 */
class LoginEntryPoint implements AuthenticationEntryPointInterface
{
	private $router;
	/**
	 * @var string
	 */
	private $loginRoute;

	/**
	 * LoginEntryPoint constructor.
	 * @param Router $router
	 */
	public function __construct(Router $router, string $loginRoute)
	{
		$this->router = $router;
		$this->loginRoute = $loginRoute;
	}

	/**
	 * Returns a response that directs the user to authenticate.
	 *
	 * This is called when an anonymous request accesses a resource that
	 * requires authentication. The job of this method is to return some
	 * response that "helps" the user start into the authentication process.
	 *
	 * Examples:
	 *  A) For a form login, you might redirect to the login page
	 *      return new RedirectResponse('/login');
	 *  B) For an API token authentication system, you return a 401 response
	 *      return new Response('Auth header required', 401);
	 *
	 * @param Request $request The request that resulted in an AuthenticationException
	 * @param AuthenticationException $authException The exception that started the authentication process
	 *
	 * @return Response
	 */
	public function start(Request $request, AuthenticationException $authException = null)
	{
		$route = $request->get('_route');
		return new RedirectResponse($this->router->generate($this->loginRoute));
	}
}