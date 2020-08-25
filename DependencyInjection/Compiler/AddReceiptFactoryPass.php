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

use Darvin\PaymentBundle\DependencyInjection\DarvinPaymentExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add receipt factory to pool compiler pass
 */
class AddReceiptFactoryPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $registry = $container->getDefinition('darvin_payment.receipt.receipt_factory_registry');

        foreach (array_keys($container->findTaggedServiceIds(DarvinPaymentExtension::TAG_RECEIPT_FACTORY)) as $id) {
            $registry->addMethodCall('addFactory', [new Reference($id)]);
        }
    }
}
