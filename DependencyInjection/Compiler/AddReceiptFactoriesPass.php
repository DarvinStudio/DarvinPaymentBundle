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
 * Add receipt factories to registry compiler pass
 */
class AddReceiptFactoriesPass implements CompilerPassInterface
{
    public const TAG_RECEIPT_FACTORY = 'darvin_payment.receipt_factory';

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $registry = $container->getDefinition('darvin_payment.receipt.factory_registry');

        foreach (array_keys($container->findTaggedServiceIds(self::TAG_RECEIPT_FACTORY)) as $id) {
            $registry->addMethodCall('addFactory', [new Reference($id)]);
        }
    }
}
