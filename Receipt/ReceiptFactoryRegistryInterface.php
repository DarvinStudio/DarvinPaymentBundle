<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2018, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Receipt;

use Darvin\PaymentBundle\Entity\PaymentInterface;

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
     * @param \Darvin\PaymentBundle\Entity\PaymentInterface $payment
     *
     * @return \Darvin\PaymentBundle\Receipt\ReceiptFactoryInterface
     *
     * @throws \Darvin\PaymentBundle\Receipt\Exception\FactoryNotExistException
     */
    public function getFactory(PaymentInterface $payment): ReceiptFactoryInterface;

    /**
     * @param PaymentInterface $payment
     *
     * @return bool
     */
    public function hasFactory(PaymentInterface $payment): bool;
}
