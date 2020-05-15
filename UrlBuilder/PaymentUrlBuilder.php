<?php declare(strict_types=1);
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

class PaymentUrlBuilder implements PaymentUrlBuilderInterface
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
    public function getAuthorizationUrl(PaymentInterface $payment, string $gatewayName): string
    {
        throw new ActionNotImplementedException('authorization');
    }

    /**
     * @inheritDoc
     */
    public function getCaptureUrl(PaymentInterface $payment, string $gatewayName): string
    {
        throw new ActionNotImplementedException('capture');
    }


    /**
     * @inheritDoc
     */
    public function getPurchaseUrl(PaymentInterface $payment, string $gatewayName): string
    {
        return $this->router->generate('darvin_payment_payment_purchase',
            [
                'id'          => $payment->getId(),
                'gatewayName' => $gatewayName,
            ],
            RouterInterface::ABSOLUTE_URL
        );
    }

    /**
     * @inheritDoc
     */
    public function getSuccessUrl(PaymentInterface $payment, string $gatewayName, $action = 'purchase'): string
    {
        if (!$payment->getActionToken()) {
            throw new \LogicException('Action token must be set for payment');
        }

        if ($action == 'purchase') {
            return $this->router->generate('darvin_payment_payment_success_purchase', [
                'gatewayName' => $gatewayName,
                'token'       => $payment->getActionToken()
            ], RouterInterface::ABSOLUTE_URL);
        }

        throw new ActionNotImplementedException($action);
    }

    /**
     * @inheritDoc
     */
    public function getCanceledUrl(PaymentInterface $payment, string $gatewayName, $action = 'purchase'): string
    {
        if (!$payment->getActionToken()) {
            throw new \LogicException('Action token must be set for payment');
        }

        if ($action == 'purchase') {
            return $this->router->generate('darvin_payment_payment_cancled_purchase', [
                'gatewayName' => $gatewayName,
                'token'       => $payment->getActionToken()
            ], RouterInterface::ABSOLUTE_URL);
        }

        throw new ActionNotImplementedException($action);
    }

    /**
     * @inheritDoc
     */
    public function getFailedUrl(PaymentInterface $payment, string $gatewayName, $action = 'purchase'): string
    {
        if (!$payment->getActionToken()) {
            throw new \LogicException('Action token must be set for payment');
        }

        if ($action == 'purchase') {
            return $this->router->generate('darvin_payment_payment_failed_purchase', [
                'gatewayName' => $gatewayName,
                'token'       => $payment->getActionToken()
            ], RouterInterface::ABSOLUTE_URL);
        }

        throw new ActionNotImplementedException($action);
    }

    /**
     * @inheritDoc
     */
    public function getRefundUrl(PaymentInterface $payment, string $gateway): string
    {
        throw new ActionNotImplementedException('refund');
    }

    /**
     * @inheritDoc
     */
    public function getNotifyUrl(PaymentInterface $payment, string $gateway): string
    {
        throw new ActionNotImplementedException('notify');
    }
}
