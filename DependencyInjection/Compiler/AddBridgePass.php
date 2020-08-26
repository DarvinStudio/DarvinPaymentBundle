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
 * Add bridge to gateway factory
 */
class AddBridgePass implements CompilerPassInterface
{
    private const GATEWAY_FACTORY = 'darvin_payment.gateway.gateway_factory';
    private const PREFIX_BRIDGE   = 'darvin_payment.bridge.';

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getParameter('darvin_payment.bridges') as $name => $attr) {
            if ($attr['enabled']) {
                $container->getDefinition(self::GATEWAY_FACTORY)->addMethodCall('addBridge', [
                    $name,
                    new Reference(self::PREFIX_BRIDGE.$name)
                ]);
            }
        }
    }
}
