<?php declare(strict_types=1);

namespace Darvin\PaymentBundle\Command;

use Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface;
use Darvin\PaymentBundle\PaymentManager\PaymentManagerInterface;
use Darvin\PaymentBundle\UrlBuilder\PaymentUrlBuilderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\VarDumper;

class TestCommand extends Command
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

        $manager = $container->get(PaymentManagerInterface::class);
        $payment = $manager->findById(41);

        $output->writeln($container->get(PaymentUrlBuilderInterface::class)->getPurchaseUrl($payment));
    }
}
