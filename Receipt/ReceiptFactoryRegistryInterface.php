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
 * Interface for registry of receipt factories
 */
interface ReceiptFactoryRegistryInterface
{
    /**
     * @param \Darvin\PaymentBundle\Receipt\ReceiptFactoryInterface $receiptFactory Receipt Factory
     *
     * @throws \InvalidArgumentException
     */
    public function addFactory(ReceiptFactoryInterface $receiptFactory): void;

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment
     *
     * @return \Darvin\PaymentBundle\Receipt\ReceiptFactoryInterface
     *
     * @throws \InvalidArgumentException
     */
    public function getFactory(Payment $payment): ReceiptFactoryInterface;

    /**
     * @param Payment $payment
     *
     * @return bool
     */
    public function hasFactory(Payment $payment): bool;
}
