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
 * Add state to state provider compiler pass
 */
class AddStatePass implements CompilerPassInterface
{
    private const SERVICE_STATE_PROVIDER = 'darvin_payment.state.provider';

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getParameter('darvin_payment.mailer.states') as $name => $value) {
            $container->getDefinition(self::SERVICE_STATE_PROVIDER)->addMethodCall('addConfig', [
                $name,
                $value,
            ]);
        }
    }
}