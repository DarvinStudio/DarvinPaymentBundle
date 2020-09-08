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
        if ($payment->getActionToken() === null) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_authorize', [
            'gatewayName' => $gatewayName,
            'token'       => $payment->getActionToken(),
        ],
            RouterInterface::ABSOLUTE_URL
        );
    }

    /**
     * @inheritDoc
     */
    public function getPurchaseUrl(Payment $payment, string $gatewayName): string
    {
        if ($payment->getActionToken() === null) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_purchase', [
            'gatewayName' => $gatewayName,
            'token'       => $payment->getActionToken(),
        ],
            RouterInterface::ABSOLUTE_URL
        );
    }

    /**
     * @inheritDoc
     */
    public function getCompleteAuthorizeUrl(Payment $payment): string
    {
        if ($payment->getActionToken() === null) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_complete_authorize', [
            'token' => $payment->getActionToken()
        ], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * @inheritDoc
     */
    public function getCaptureUrl(Payment $payment): string
    {
        if ($payment->getActionToken() === null) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_admin_capture', [
            'token' => $payment->getActionToken()
        ], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * @inheritDoc
     */
    public function getCompletePurchaseUrl(Payment $payment): string
    {
        if ($payment->getActionToken() === null) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_complete_purchase', [
            'token' => $payment->getActionToken()
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

    /**
     * @inheritDoc
     */
    public function getApproveUrl(Payment $payment): string
    {
        if ($payment->getActionToken() === null) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_admin_approve', [
            'token' => $payment->getActionToken()
        ], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * @inheritDoc
     */
    public function getCancelUrl(Payment $payment): string
    {
        if ($payment->getActionToken() === null) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_cancel', [
            'token' => $payment->getActionToken()
        ], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * @inheritDoc
     */
    public function getSuccessUrl(Payment $payment): string
    {
        if ($payment->getActionToken() === null) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_success', [
            'token' => $payment->getActionToken()
        ], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * @inheritDoc
     */
    public function getFailUrl(Payment $payment): string
    {
        if ($payment->getActionToken() === null) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_fail', [
            'token' => $payment->getActionToken()
        ], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * @inheritDoc
     */
    public function getErrorUrl(Payment $payment): string
    {
        if ($payment->getActionToken() === null) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_error', [
            'token' => $payment->getActionToken()
        ], RouterInterface::ABSOLUTE_URL);
    }
}
