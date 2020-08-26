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

use Darvin\PaymentBundle\Entity\Payment;

/**
 * Registry of receipt factories
 */
class ReceiptFactoryRegistry implements ReceiptFactoryRegistryInterface
{
    /**
     * @var \Darvin\PaymentBundle\Receipt\ReceiptFactoryInterface[]|null
     */
    private $receiptFactories = [];

    /**
     * @inheritDoc
     */
    public function addFactory(ReceiptFactoryInterface $receiptFactory): void
    {
        $alias = $receiptFactory->getName();

        if (isset($this->receiptFactories[$alias])) {
            throw new \InvalidArgumentException(sprintf('Receipt factory "%s" already exists.', $alias));
        }

        $this->receiptFactories[$alias] = $receiptFactory;
    }

    /**
     * @inheritDoc
     */
    public function getFactory(Payment $payment): ReceiptFactoryInterface
    {
        foreach ($this->receiptFactories as $receiptFactory) {
            if ($receiptFactory->support($payment)) {
                return $receiptFactory;
            }
        }

        throw new \InvalidArgumentException(sprintf('Can\'t get receipt factory for payment №"%s".', $payment->getId()));
    }

    /**
     * @inheritDoc
     */
    public function hasFactory(Payment $payment): bool
    {
        foreach ($this->receiptFactories as $receiptFactory) {
            if ($receiptFactory->support($payment)) {
                return true;
            }
        }

        return false;
    }
}
