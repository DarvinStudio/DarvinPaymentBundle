<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2021, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Bridge;

use Darvin\PaymentBundle\Entity\Payment;

/**
 * YooKassa bridge
 */
class YookassaBridge extends AbstractBridge
{
    /**
     * {@inheritDoc}
     */
    public function getGatewayClassName(): string
    {
        // TODO: Implement getGatewayClassName() method.
    }

    /**
     * {@inheritDoc}
     */
    public function getGatewayName(): string
    {
        // TODO: Implement getGatewayName() method.
    }

    /**
     * {@inheritDoc}
     */
    public function getSessionTimeout(): int
    {
        // TODO: Implement getSessionTimeout() method.
    }

    /**
     * {@inheritDoc}
     */
    public function authorizeParameters(Payment $payment): array
    {
        // TODO: Implement authorizeParameters() method.
    }

    /**
     * {@inheritDoc}
     */
    public function completeAuthorizeParameters(Payment $payment): array
    {
        // TODO: Implement completeAuthorizeParameters() method.
    }

    /**
     * {@inheritDoc}
     */
    public function captureParameters(Payment $payment): array
    {
        // TODO: Implement captureParameters() method.
    }

    /**
     * {@inheritDoc}
     */
    public function purchaseParameters(Payment $payment): array
    {
        // TODO: Implement purchaseParameters() method.
    }

    /**
     * {@inheritDoc}
     */
    public function completePurchaseParameters(Payment $payment): array
    {
        // TODO: Implement completePurchaseParameters() method.
    }

    /**
     * {@inheritDoc}
     */
    public function voidParameters(Payment $payment): array
    {
        // TODO: Implement voidParameters() method.
    }

    /**
     * {@inheritDoc}
     */
    public function refundParameters(Payment $payment): array
    {
        // TODO: Implement refundParameters() method.
    }

    /**
     * {@inheritDoc}
     */
    public function acceptNotificationParameters(Payment $payment): array
    {
        // TODO: Implement acceptNotificationParameters() method.
    }
}
