<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add bridges to gateway factory
 */
class AddBridgesPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $gatewayFactory = $container->getDefinition('darvin_payment.gateway.factory');

        foreach ($container->getParameter('darvin_payment.bridges') as $name => $attr) {
            if ($attr['enabled']) {
                $gatewayFactory->addMethodCall('addBridge', [$name, new Reference(sprintf('darvin_payment.bridge.%s', $name))]);
            }
        }
    }
}
