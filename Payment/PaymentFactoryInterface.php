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
     * @param int         $orderId          Order ID
     * @param string      $orderEntityClass Class of order entity
     * @param string      $amount           Amount
     * @param string|null $currencyCode     Currency code
     *
     * @return Payment
     */
    public function createPayment(
        int $orderId,
        string $orderEntityClass,
        string $amount,
        ?string $currencyCode
    ): Payment;
}
