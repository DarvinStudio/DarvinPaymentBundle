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
use Symfony\Component\Routing\RouterInterface;

/**
 * Payment URL builder
 */
class PaymentUrlBuilder implements PaymentUrlBuilderInterface
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router Router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function getPurchaseUrl(Payment $payment, string $gatewayName): string
    {
        if ($payment->getToken() === null) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_purchase', [
            'gatewayName' => $gatewayName,
            'token'       => $payment->getToken(),
        ], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * {@inheritDoc}
     */
    public function getCompleteUrl(Payment $payment): string
    {
        if ($payment->getToken() === null) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_complete', [
            'token' => $payment->getToken()
        ], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * {@inheritDoc}
     */
    public function getCaptureUrl(Payment $payment): string
    {
        if ($payment->getToken() === null) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_admin_capture', [
            'token' => $payment->getToken()
        ], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * {@inheritDoc}
     */
    public function getRefundUrl(Payment $payment): string
    {
        if ($payment->getToken() === null) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_admin_refund', [
            'token' => $payment->getToken()
        ], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * {@inheritDoc}
     */
    public function getNotifyUrl(Payment $payment): string
    {
        throw new \RuntimeException('Not implemented.');
    }

    /**
     * {@inheritDoc}
     */
    public function getApproveUrl(Payment $payment): string
    {
        if ($payment->getToken() === null) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_admin_approve', [
            'token' => $payment->getToken()
        ], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * {@inheritDoc}
     */
    public function getCancelUrl(Payment $payment): string
    {
        if ($payment->getToken() === null) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_cancel', [
            'token' => $payment->getToken()
        ], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * {@inheritDoc}
     */
    public function getVoidUrl(Payment $payment): string
    {
        if ($payment->getToken() === null) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_admin_void', [
            'token' => $payment->getToken()
        ], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * {@inheritDoc}
     */
    public function getSuccessUrl(Payment $payment): string
    {
        if ($payment->getToken() === null) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_success', [
            'token' => $payment->getToken()
        ], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * {@inheritDoc}
     */
    public function getFailUrl(Payment $payment): string
    {
        if ($payment->getToken() === null) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_fail', [
            'token' => $payment->getToken()
        ], RouterInterface::ABSOLUTE_URL);
    }

    /**
     * {@inheritDoc}
     */
    public function getErrorUrl(Payment $payment): string
    {
        if ($payment->getToken() === null) {
            throw new \LogicException('Action token must be set for payment');
        }

        return $this->router->generate('darvin_payment_error', [
            'token' => $payment->getToken()
        ], RouterInterface::ABSOLUTE_URL);
    }
}
