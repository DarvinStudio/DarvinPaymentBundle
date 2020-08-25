<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Payment\Manager;

use Darvin\PaymentBundle\Entity\PaymentInterface;

/**
 * Payment manager interface
 */
interface PaymentManagerInterface
{
    /**
     * @param int         $orderId          Order ID
     * @param string      $orderEntityClass Class of order entity
     * @param string      $amount           Amount
     * @param string      $currencyCode     Currency code
     * @param int|null    $clientId         Client ID
     * @param string|null $clientEmail      Client Email
     * @param string|null $description      Description
     * @param array|null  $options          Options
     *
     * @return PaymentInterface
     */
    public function createPayment(
        int $orderId,
        string $orderEntityClass,
        string $amount,
        string $currencyCode,
        ?int $clientId,
        ?string $clientEmail,
        ?string $description,
        ?array $options
    ): PaymentInterface;

    /**
     * @param PaymentInterface $payment
     * @param string           $reference
     *
     * @return void
     */
    public function setTransactionReference(PaymentInterface $payment, string $reference): void;
}
