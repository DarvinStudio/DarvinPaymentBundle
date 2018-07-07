<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 07.07.2018
 * Time: 1:34
 */

namespace Darvin\PaymentBundle\Gateway\ParametersBridge;


use Darvin\PaymentBundle\Entity\PaymentInterface;

class TestGatewayParametersBridge extends AbstractGatewayParametersBridge
{

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function authorizationParameters(PaymentInterface $payment)
    {
        // TODO: Implement authorizationParameters() method.
    }

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function captureParameters(PaymentInterface $payment)
    {
        // TODO: Implement captureParameters() method.
    }

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function purchaseParameters(PaymentInterface $payment)
    {
        // TODO: Implement purchaseParameters() method.
    }

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function refundParameters(PaymentInterface $payment)
    {
        // TODO: Implement refundParameters() method.
    }

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function acceptNotificationParameters(PaymentInterface $payment)
    {
        // TODO: Implement acceptNotificationParameters() method.
    }
}