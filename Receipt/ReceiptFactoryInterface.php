<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Receipt;

use Darvin\PaymentBundle\Entity\Payment;

/**
 * Receipt factory interface
 */
interface ReceiptFactoryInterface
{
    /**
     * @param Payment $payment
     *
     * @return array
     *
     * @throws \Darvin\PaymentBundle\Receipt\Exception\CantCreateReceiptException
     */
    public function createReceipt(Payment $payment): array;

    /**
     * @param Payment $payment
     *
     * @return bool
     */
    public function support(Payment $payment): bool;

    /**
     * @return string
     */
    public function getName(): string;
}
