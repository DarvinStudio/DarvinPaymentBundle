<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 07.07.2018
 * Time: 1:34
 */

namespace Darvin\PaymentBundle\Bridge;

use Darvin\OmnipayTelr\TelrGateway;
use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\UrlBuilder\PaymentUrlBuilderInterface;

class TelrBridge extends AbstractBridge
{
    /**
     * @var PaymentUrlBuilderInterface
     */
    protected $urlBuilder;

    /**
     * TelrBridge constructor.
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
    public function getGatewayClassName(): string
    {
        return TelrGateway::class;
    }

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function authorizationParameters(PaymentInterface $payment): array
    {
        // TODO: Implement authorizationParameters() method.
    }

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function captureParameters(PaymentInterface $payment): array
    {
        // TODO: Implement captureParameters() method.
    }

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function purchaseParameters(PaymentInterface $payment): array
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
    public function completePurchaseParameters(PaymentInterface $payment): array
    {
        return [
            'order_ref' => $payment->getTransactionRef()
        ];
    }


    /**
     * @param PaymentInterface 3$payment
     *
     * @return array
     */
    public function refundParameters(PaymentInterface $payment): array
    {
        // TODO: Implement refundParameters() method.
    }

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function acceptNotificationParameters(PaymentInterface $payment): array
    {
        // TODO: Implement acceptNotificationParameters() method.
    }
}