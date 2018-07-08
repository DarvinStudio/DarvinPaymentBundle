<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.07.2018
 * Time: 23:04
 */

namespace Darvin\PaymentBundle\Gateway\ParametersBridge;


use Darvin\PaymentBundle\Entity\PaymentInterface;

interface GatewayParametersBridgeInterface
{
    /**
     * @return string
     */
    public function getGatewayClassName();

    /**
     * @return array
     */
    public function initializationParameters();

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function authorizationParameters(PaymentInterface $payment);

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function captureParameters(PaymentInterface $payment);

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function purchaseParameters(PaymentInterface $payment);

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function completePurchaseParameters(PaymentInterface $payment);

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function refundParameters(PaymentInterface $payment);

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function acceptNotificationParameters(PaymentInterface $payment);
}