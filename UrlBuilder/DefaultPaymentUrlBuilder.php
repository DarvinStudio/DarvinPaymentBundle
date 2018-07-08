<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 07.07.2018
 * Time: 1:57
 */

namespace Darvin\PaymentBundle\UrlBuilder;


use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\UrlBuilder\Exception\ActionNotImplementedException;
use Symfony\Component\Routing\RouterInterface;

class DefaultPaymentUrlBuilder implements PaymentUrlBuilderInterface
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * DefaultPaymentUrlBuilder constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @inheritDoc
     */
    public function getAuthorizationUrl(PaymentInterface $payment, $gateway = null)
    {
        throw new ActionNotImplementedException('authorization');
    }

    /**
     * @inheritDoc
     */
    public function getCaptureUrl(PaymentInterface $payment, $gateway = null)
    {
        throw new ActionNotImplementedException('capture');
    }


    /**
     * @inheritDoc
     */
    public function getPurchaseUrl(PaymentInterface $payment, $gateway = null)
    {
        return $this->router->generate('darvin_payment_payment_purchase',
            [
                'id'          => $payment->getId(),
                'gatewayName' => $gateway,
            ],
            RouterInterface::ABSOLUTE_URL
        );
    }

    /**
     * @inheritDoc
     */
    public function getSuccessUrl(PaymentInterface $payment, $gateway = null, $action = 'purchase')
    {
        if (!$payment->getActionToken()) {
            throw new \LogicException('Action token must be set for payment');
        }

        if ($action == 'purchase') {
            return $this->router->generate('darvin_payment_payment_success_purchase', [
                'gatewayName' => $gateway,
                'token'        => $payment->getActionToken()
            ], RouterInterface::ABSOLUTE_URL);
        }

        throw new ActionNotImplementedException($action);
    }

    /**
     * @inheritDoc
     */
    public function getCanceledUrl(PaymentInterface $payment, $gateway = null, $action = 'purchase')
    {
        if (!$payment->getActionToken()) {
            throw new \LogicException('Action token must be set for payment');
        }

        if ($action == 'purchase') {
            return $this->router->generate('darvin_payment_payment_cancled_purchase', [
                'gatewayName' => $gateway,
                'token'        => $payment->getActionToken()
            ], RouterInterface::ABSOLUTE_URL);
        }

        throw new ActionNotImplementedException($action);
    }

    /**
     * @inheritDoc
     */
    public function getFailedUrl(PaymentInterface $payment, $gateway = null, $action = 'purchase')
    {
        if (!$payment->getActionToken()) {
            throw new \LogicException('Action token must be set for payment');
        }

        if ($action == 'purchase') {
            return $this->router->generate('darvin_payment_payment_failed_purchase', [
                'gatewayName'  => $gateway,
                'token'        => $payment->getActionToken()
            ], RouterInterface::ABSOLUTE_URL);
        }

        throw new ActionNotImplementedException($action);
    }

    /**
     * @inheritDoc
     */
    public function getRefundUrl(PaymentInterface $payment, $gateway = null)
    {
        throw new ActionNotImplementedException('refund');
    }

    /**
     * @inheritDoc
     */
    public function getNotifyUrl(PaymentInterface $payment, $gateway = null)
    {
        throw new ActionNotImplementedException('notify');
    }
}