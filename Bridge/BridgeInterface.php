<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Bridge;

use Darvin\PaymentBundle\Entity\PaymentInterface;

interface BridgeInterface
{
    /**
     * @return string
     */
    public function getGatewayClassName(): string;

    /**
     * @return array
     */
    public function initializationParameters(): array;

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function authorizationParameters(PaymentInterface $payment): array;

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function completeAuthorizationParameters(PaymentInterface $payment): array;

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function captureParameters(PaymentInterface $payment): array;

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function purchaseParameters(PaymentInterface $payment): array;

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function completePurchaseParameters(PaymentInterface $payment): array;

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function refundParameters(PaymentInterface $payment): array;

    /**
     * @param PaymentInterface $payment
     *
     * @return array
     */
    public function acceptNotificationParameters(PaymentInterface $payment): array;
}
