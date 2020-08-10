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
class AddPaymentStatusProviderPass implements CompilerPassInterface
{
    private const CONFIG_MAILER_PAYMENT_STATUSES = 'darvin_payment.mailer.payment_statuses';
    private const PAYMENT_STATUS_PROVIDER = 'darvin_payment.provider.payment_statuses';

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::CONFIG_MAILER_PAYMENT_STATUSES)) {
            return;
        }

        $container->getDefinition(self::PAYMENT_STATUS_PROVIDER)->addMethodCall('addConfigs', [
            $container->getParameter(self::CONFIG_MAILER_PAYMENT_STATUSES)
        ]);
    }
}
