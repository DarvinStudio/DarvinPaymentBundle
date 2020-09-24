<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Bridge;

use Darvin\PaymentBundle\Entity\Payment;

/**
 * Bridge
 */
interface BridgeInterface
{
    /**
     * @return string
     */
    public function getGatewayClassName(): string;

    /**
     * @return string
     */
    public function getGatewayName(): string;

    /**
     * @return array
     */
    public function initializationParameters(): array;

    /**
     * @return int
     */
    public function getSessionTimeout(): int;

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment
     *
     * @return array
     */
    public function authorizeParameters(Payment $payment): array;

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment
     *
     * @return array
     */
    public function completeAuthorizeParameters(Payment $payment): array;

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment
     *
     * @return array
     */
    public function captureParameters(Payment $payment): array;

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment
     *
     * @return array
     */
    public function purchaseParameters(Payment $payment): array;

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment
     *
     * @return array
     */
    public function completePurchaseParameters(Payment $payment): array;

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment
     *
     * @return array
     */
    public function voidParameters(Payment $payment): array;

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment
     *
     * @return array
     */
    public function refundParameters(Payment $payment): array;

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment
     *
     * @return array
     */
    public function acceptNotificationParameters(Payment $payment): array;
}
