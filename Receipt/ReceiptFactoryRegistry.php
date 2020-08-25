<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Receipt;

use Darvin\PaymentBundle\Entity\PaymentInterface;

/**
 * Registry of receipt factories
 */
class ReceiptFactoryRegistry implements ReceiptFactoryRegistryInterface
{
    /**
     * @var \Darvin\PaymentBundle\Receipt\ReceiptFactoryInterface[]|null
     */
    private $receiptFactories;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->receiptFactories = [];
    }

    /**
     * @param \Darvin\PaymentBundle\Receipt\ReceiptFactoryInterface $receiptFactory Receipt Factory
     *
     * @throws \InvalidArgumentException
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
    public function getFactory(PaymentInterface $payment): ReceiptFactoryInterface
    {
        foreach ($this->receiptFactories as $receiptFactory) {
            if ($receiptFactory->support($payment)) {
                return $receiptFactory;
            }
        }

        throw new \Darvin\PaymentBundle\Receipt\Exception\FactoryNotExistException($payment);
    }

    /**
     * @inheritDoc
     */
    public function hasFactory(PaymentInterface $payment): bool
    {
        foreach ($this->receiptFactories as $receiptFactory) {
            if ($receiptFactory->support($payment)) {
                return true;
            }
        }

        return false;
    }
}
