<?php declare(strict_types=1);

namespace Darvin\PaymentBundle;

use Darvin\PaymentBundle\DependencyInjection\Compiler\AddBridgePass;
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
    }

}
