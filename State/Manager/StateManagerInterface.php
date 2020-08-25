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

use Darvin\PaymentBundle\Entity\PaymentInterface;

/**
 * State manager interface
 */
interface StateManagerInterface
{
    /**
     * @param PaymentInterface $payment Payment
     *
     * @return void
     */
    public function markAsNew(PaymentInterface $payment): void;

    /**
     * @param PaymentInterface $payment Payment
     *
     * @return void
     */
    public function markAsPending(PaymentInterface $payment): void;

    /**
     * @param PaymentInterface $payment Payment
     *
     * @return void
     */
    public function markAsCompleted(PaymentInterface $payment): void;

    /**
     * @param PaymentInterface $payment Payment
     *
     * @return void
     */
    public function markAsCanceled(PaymentInterface $payment): void;

    /**
     * @param PaymentInterface $payment Payment
     *
     * @return void
     */
    public function markAsFailed(PaymentInterface $payment): void;

    /**
     * @param PaymentInterface $payment Payment
     *
     * @return void
     */
    public function markAsRefund(PaymentInterface $payment): void;
}
