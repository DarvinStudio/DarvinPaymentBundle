<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Receipt;

use Darvin\PaymentBundle\Bridge\BridgeInterface;
use Darvin\PaymentBundle\Entity\Payment;

/**
 * Registry of receipt factories
 */
class ReceiptFactoryRegistry implements ReceiptFactoryRegistryInterface
{
    /**
     * @var \Darvin\PaymentBundle\Receipt\ReceiptFactoryInterface[]
     */
    private $factories;

    /**
     * Receipt factory registry constructor.
     */
    public function __construct()
    {
        $this->factories = [];
    }

    /**
     * @param \Darvin\PaymentBundle\Receipt\ReceiptFactoryInterface $factory Receipt factory
     *
     * @throws \InvalidArgumentException
     */
    public function addFactory(ReceiptFactoryInterface $factory): void
    {
        $name = $factory->getName();

        if (isset($this->factories[$name])) {
            throw new \InvalidArgumentException(sprintf('Receipt factory "%s" already exists.', $name));
        }

        $this->factories[$name] = $factory;
    }

    /**
     * {@inheritDoc}
     */
    public function getFactory(Payment $payment, BridgeInterface $bridge): ReceiptFactoryInterface
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($payment, $bridge)) {
                return $factory;
            }
        }

        throw new \InvalidArgumentException(sprintf('Can\'t get receipt factory for payment "%s".', $payment->__toString()));
    }

    /**
     * {@inheritDoc}
     */
    public function hasFactory(Payment $payment, BridgeInterface $bridge): bool
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($payment, $bridge)) {
                return true;
            }
        }

        return false;
    }
}
