<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 07.07.2018
 * Time: 1:12
 */

namespace Darvin\PaymentBundle\DependencyInjection\Compiler;

use Darvin\PaymentBundle\Bridge\TelrBridge;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class AddBridgePass implements CompilerPassInterface
{
    private const GATEWAY_FACTORY = 'darvin_payment.gateway.gateway_factory';
    private const URL_BUILDER     = 'darvin_payment.url_builder.payment_url_builder';
    private const PREFIX_BRIDGE   = 'darvin_payment.bridge.';

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::GATEWAY_FACTORY)) {
            return;
        }

        foreach ($container->getParameter('darvin_payment.bridges') as $name => $attr) {
//            if (!$attr['enabled']) {
//                continue;
//            }
//
//            unset($attr['enabled']);

            $container->getDefinition(self::GATEWAY_FACTORY)->addMethodCall('addBridge', [
                $name,
                new Reference(self::PREFIX_BRIDGE.$name)
            ]);
        }
    }
}
