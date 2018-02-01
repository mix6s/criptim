<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 2/1/18
 * Time: 3:05 PM
 */

namespace DomainBundle;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ExchangePass implements CompilerPassInterface
{
	public function process(ContainerBuilder $container)
	{
		if (!$container->has('ExchangeRepository')) {
			return;
		}

		$definition = $container->findDefinition('ExchangeRepository');
		$taggedServices = $container->findTaggedServiceIds('domain.exchange');

		foreach ($taggedServices as $id => $tags) {
			$definition->addMethodCall('addExchange', array(new Reference($id)));
		}
	}
}