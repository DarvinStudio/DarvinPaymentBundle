<?php

namespace Darvin\PaymentBundle\Command;

use Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\VarDumper;

class TestCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('darvin_payment:test')
            ->setDescription('Hello PhpStorm')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $bridge = $container->get(GatewayFactoryInterface::class)->getGatewayParametersBridge('test');

        VarDumper::dump($bridge);

    }
}
