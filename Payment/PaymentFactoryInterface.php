<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Payment;

use Darvin\PaymentBundle\Entity\Payment;

/**
 * Payment factory interface
 */
interface PaymentFactoryInterface
{
    /**
     * @param string      $orderId          Order ID
     * @param string      $orderEntityClass Class of order entity
     * @param string      $orderNumber      Public order ID
     * @param string      $amount           Amount
     * @param string|null $currencyCode     Currency code
     * @param string|null $clientId         Client ID
     * @param string|null $clientEmail      Client email
     *
     * @return Payment
     */
    public function createPayment(
        string $orderId,
        string $orderEntityClass,
        string $orderNumber,
        string $amount,
        ?string $currencyCode,
        ?string $clientId,
        ?string $clientEmail
    ): Payment;
}
