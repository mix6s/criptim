<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{

	public const APP_TYPE_CONSOLE = 'console';
	public const APP_TYPE_CRIPTIM = 'criptim';
	public const APP_TYPE_FINTOBIT = 'fintobit';
	public const APP_TYPE_CONTROL = 'control';

	private $appType;

	public function __construct(string $environment, bool $debug, string $appType)
	{
		$this->appType = $appType;
		parent::__construct($environment, $debug);
	}

	public function registerBundles(): array
	{
		$bundles = [
			new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
			new Symfony\Bundle\SecurityBundle\SecurityBundle(),
			new Symfony\Bundle\TwigBundle\TwigBundle(),
			new Symfony\Bundle\MonologBundle\MonologBundle(),
			new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
			new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
			new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
			new FOS\UserBundle\FOSUserBundle(),
			new AppBundle\AppBundle(),
			new DomainBundle\DomainBundle(),
			new \ControlBundle\ControlBundle(),
			new CriptimBundle\CriptimBundle(),
			new FintobitBundle\FintobitBundle(),
			new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
		];

		if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
			$bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
			$bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
			$bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();

			if ('dev' === $this->getEnvironment()) {
				$bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
				$bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
			}
		}

		return $bundles;
	}

	public function getProjectDir()
	{
		return dirname(__DIR__);
	}

	public function getRootDir()
	{
		return __DIR__;
	}

	public function getCacheDir()
	{
		return dirname(__DIR__) . '/var/cache/' . $this->getAppType() . '/' . $this->getEnvironment();
	}

	public function getLogDir()
	{
		return dirname(__DIR__) . '/var/logs/' . $this->getAppType();
	}

	public function registerContainerConfiguration(LoaderInterface $loader)
	{
		$resourse = $this->getRootDir() .
			'/config/' . $this->getAppType() .
			'/config_' . $this->getEnvironment() . '.yml';

		$loader->load($resourse);
	}

	private function getAppType(): string
	{
		return $this->appType;
	}

	/**
	 * Returns the kernel parameters.
	 *
	 * @return array An array of kernel parameters
	 */
	protected function getKernelParameters()
	{
		return array_merge([
			'kernel.app_type' => $this->appType,
		], parent::getKernelParameters());
	}

	protected function build(ContainerBuilder $container)
	{
		$container->addCompilerPass(new \DomainBundle\ExchangePass());
	}
}
