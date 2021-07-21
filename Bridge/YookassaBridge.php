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
use Omnipay\YooKassa\Gateway;

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
        return Gateway::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getGatewayName(): string
    {
        return 'yookassa';
    }

    /**
     * {@inheritDoc}
     */
    public function getSessionTimeout(): int
    {
        throw new \RuntimeException('Not implemented.');
    }

    /**
     * {@inheritDoc}
     */
    public function authorizeParameters(Payment $payment): array
    {
        throw new \RuntimeException('Not implemented.');
    }

    /**
     * {@inheritDoc}
     */
    public function completeAuthorizeParameters(Payment $payment): array
    {
        throw new \RuntimeException('Not implemented.');
    }

    /**
     * {@inheritDoc}
     */
    public function captureParameters(Payment $payment): array
    {
        throw new \RuntimeException('Not implemented.');
    }

    /**
     * {@inheritDoc}
     */
    public function purchaseParameters(Payment $payment): array
    {
        throw new \RuntimeException('Not implemented.');
    }

    /**
     * {@inheritDoc}
     */
    public function completePurchaseParameters(Payment $payment): array
    {
        throw new \RuntimeException('Not implemented.');
    }

    /**
     * {@inheritDoc}
     */
    public function voidParameters(Payment $payment): array
    {
        throw new \RuntimeException('Not implemented.');
    }

    /**
     * {@inheritDoc}
     */
    public function refundParameters(Payment $payment): array
    {
        throw new \RuntimeException('Not implemented.');
    }

    /**
     * {@inheritDoc}
     */
    public function acceptNotificationParameters(Payment $payment): array
    {
        throw new \RuntimeException('Not implemented.');
    }
}
