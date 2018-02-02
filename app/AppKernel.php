<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{

	public const APP_TYPE_CONSOLE = 'console';
	public const APP_TYPE_CRIPTIM = 'criptim';
	public const APP_TYPE_FINTOBIT = 'fintobit';

	private $appType;

	public function __construct(string $environment, bool $debug)
	{
		$appType = getenv('APP_TYPE');
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
			new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
        ];

		if ($this->appType === self::APP_TYPE_CRIPTIM) {
			$bundles[] = new CriptimBundle\CriptimBundle();
		}

		if ($this->appType === self::APP_TYPE_FINTOBIT) {
			$bundles[] = new \FintobitBundle\FintobitBundle();
		}

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

	protected function build(ContainerBuilder $container)
	{
		$container->addCompilerPass(new \DomainBundle\ExchangePass());
	}
}
