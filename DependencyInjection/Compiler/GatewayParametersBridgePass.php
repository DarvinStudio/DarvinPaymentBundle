<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 07.07.2018
 * Time: 1:12
 */

namespace Darvin\PaymentBundle\DependencyInjection\Compiler;

use Darvin\PaymentBundle\Gateway\Factory\DefaultGatewayFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class GatewayParametersBridgePass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(DefaultGatewayFactory::class)) {
            return;
        }

        $definition = $container->getDefinition(DefaultGatewayFactory::class);
        $taggedServices = $container->findTaggedServiceIds('darvin_payment.parameters_bridge');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {

                $bridgeDefinition = $container->getDefinition($id);
                $bridgeDefinition->addMethodCall('setGatewayConfig', [
                    $container->getParameter("darvin_payment.config.gateway_parameters_bridge.{$attributes["gateway"]}")
                ]);

                $definition->addMethodCall('addGatewayParametersBridge', [
                    $attributes["gateway"],
                    new Reference($id)
                ]);
            }
        }
    }
}
