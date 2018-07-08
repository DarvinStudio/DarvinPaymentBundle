<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 07.07.2018
 * Time: 1:34
 */

namespace Darvin\PaymentBundle\Gateway\ParametersBridge;


use Darvin\OmnipayTelr\TelrGateway;
use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\UrlBuilder\PaymentUrlBuilderInterface;

class TelrGatewayParametersBridge extends AbstractGatewayParametersBridge
{
    /**
     * @var PaymentUrlBuilderInterface
     */
    protected $urlBuilder;

    /**
     * TelrGatewayParametersBridge constructor.
     *
     * @param PaymentUrlBuilderInterface $urlBuilder
     */
    public function __construct(PaymentUrlBuilderInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritDoc
     */
    public function getGatewayClassName()
    {
        return TelrGateway::class;
    }

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
        return [
            'ivp_amount'   => $payment->getAmount(),
            'ivp_currency' => $payment->getCurrencyCode(),
            'ivp_cart'     => $payment->getOrderId(),
            'ivp_desc'     => $payment->getDescription(),
            'return_auth'  => $this->urlBuilder->getSuccessUrl($payment, 'telr'),
            'return_decl'  => $this->urlBuilder->getFailedUrl($payment, 'telr'),
            'return_can'   => $this->urlBuilder->getCanceledUrl($payment, 'telr')
        ];
    }

    /**
     * @inheritDoc
     */
    public function completePurchaseParameters(PaymentInterface $payment)
    {
        return [
            'order_ref' => $payment->getTransactionRef()
        ];
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