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
 * Interface for registry of receipt factories
 */
interface ReceiptFactoryRegistryInterface
{
    /**
     * @param \Darvin\PaymentBundle\Entity\Payment         $payment Payment
     * @param \Darvin\PaymentBundle\Bridge\BridgeInterface $bridge  Bridge
     *
     * @return \Darvin\PaymentBundle\Receipt\ReceiptFactoryInterface
     * @throws \InvalidArgumentException
     */
    public function getFactory(Payment $payment, BridgeInterface $bridge): ReceiptFactoryInterface;

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment         $payment Payment
     * @param \Darvin\PaymentBundle\Bridge\BridgeInterface $bridge  Bridge
     *
     * @return bool
     */
    public function hasFactory(Payment $payment, BridgeInterface $bridge): bool;
}
