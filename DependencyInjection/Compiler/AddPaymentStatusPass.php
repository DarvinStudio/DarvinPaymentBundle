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

/**
 * Add order type providers to pool compiler pass
 */
class AddPaymentStatusPass implements CompilerPassInterface
{
    private const SERVICE_PAYMENT_STATUS_PROVIDER = 'darvin_payment.status.provider';

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_PAYMENT_STATUS_PROVIDER)) {
            return;
        }

        foreach ($container->getParameter('darvin_payment.mailer.payment_statuses') as $name => $value) {
            $container->getDefinition(self::SERVICE_PAYMENT_STATUS_PROVIDER)->addMethodCall('addConfig', [
                $name,
                $value,
            ]);
        }
    }
}
