<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.07.2018
 * Time: 23:04
 */

namespace Darvin\PaymentBundle\Bridge;

use Darvin\PaymentBundle\Entity\PaymentInterface;

interface BridgeInterface
{
    /**
     * @return string
     */
    public function getGatewayClassName(): string;

    /**
     * @return array
     */
    public function initializationParameters(): array;

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function authorizationParameters(PaymentInterface $payment): array;

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function captureParameters(PaymentInterface $payment): array;

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function purchaseParameters(PaymentInterface $payment): array;

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function completePurchaseParameters(PaymentInterface $payment): array;

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function refundParameters(PaymentInterface $payment): array;

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function acceptNotificationParameters(PaymentInterface $payment): array;
}
