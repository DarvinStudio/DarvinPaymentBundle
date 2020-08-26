<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\State\Manager;

use Darvin\PaymentBundle\Entity\Payment;

/**
 * State manager interface
 */
interface StateManagerInterface
{
    /**
     * @param Payment $payment Payment
     *
     * @return void
     */
    public function markAsNew(Payment $payment): void;

    /**
     * @param Payment $payment Payment
     *
     * @return void
     */
    public function markAsPending(Payment $payment): void;

    /**
     * @param Payment $payment Payment
     *
     * @return void
     */
    public function markAsCompleted(Payment $payment): void;

    /**
     * @param Payment $payment Payment
     *
     * @return void
     */
    public function markAsCanceled(Payment $payment): void;

    /**
     * @param Payment $payment Payment
     *
     * @return void
     */
    public function markAsFailed(Payment $payment): void;

    /**
     * @param Payment $payment Payment
     *
     * @return void
     */
    public function markAsRefund(Payment $payment): void;

    /**
     * @param Payment $payment Payment
     *
     * @return bool
     */
    public function isCompleted(Payment $payment): bool;
}
