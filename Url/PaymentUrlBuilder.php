<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Url;

use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Url\Exception\ActionNotImplementedException;
use Symfony\Component\Routing\RouterInterface;

/**
 * Payment url builder
 */
class PaymentUrlBuilder implements PaymentUrlBuilderInterface
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @inheritDoc
     */
    public function getAuthorizeUrl(Payment $payment, string $gatewayName): string
    {
        return $this->router->generate('darvin_payment_authorize', [
            'id'          => $payment->getId(),
            'gatewayName' => $gatewayName,
        ],
            RouterInterface::ABSOLUTE_URL
        );
    }

    /**
     * @inheritDoc
     */
    public function getAuthorizeSuccessUrl(Payment $payment, string $gatewayName): string
    {
        if (!$payment->getActionToken()) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_authorize_success', [
            'gatewayName' => $gatewayName,
            'token'       => $payment->getActionToken()
        ], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * @inheritDoc
     */
    public function getCaptureUrl(Payment $payment, string $gatewayName): string
    {
        throw new ActionNotImplementedException('capture');
    }

    /**
     * @inheritDoc
     */
    public function getPurchaseUrl(Payment $payment, string $gatewayName): string
    {
        return $this->router->generate('darvin_payment_purchase', [
                'id'          => $payment->getId(),
                'gatewayName' => $gatewayName,
            ],
            RouterInterface::ABSOLUTE_URL
        );
    }

    /**
     * @inheritDoc
     */
    public function getPurchaseSuccessUrl(Payment $payment, string $gatewayName): string
    {
        if (!$payment->getActionToken()) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_purchase_success', [
            'gatewayName' => $gatewayName,
            'token'       => $payment->getActionToken()
        ], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * @inheritDoc
     */
    public function getCanceledUrl(Payment $payment, string $gatewayName): string
    {
        if (!$payment->getActionToken()) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_canceled', [
            'gatewayName' => $gatewayName,
            'token'       => $payment->getActionToken()
        ], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * @inheritDoc
     */
    public function getFailedUrl(Payment $payment, string $gatewayName): string
    {
        if (!$payment->getActionToken()) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_failed', [
            'gatewayName' => $gatewayName,
            'token'       => $payment->getActionToken()
        ], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * @inheritDoc
     */
    public function getRefundUrl(Payment $payment, string $gatewayName): string
    {
        throw new ActionNotImplementedException('refund');
    }

    /**
     * @inheritDoc
     */
    public function getNotifyUrl(Payment $payment, string $gatewayName): string
    {
        throw new ActionNotImplementedException('notify');
    }
}
