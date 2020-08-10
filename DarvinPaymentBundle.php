<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle;

use Darvin\PaymentBundle\DependencyInjection\Compiler\AddBridgePass;
use Darvin\PaymentBundle\DependencyInjection\Compiler\AddPaymentStatusProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DarvinPaymentBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new AddBridgePass());
        $container->addCompilerPass(new AddPaymentStatusProviderPass());
    }

}
