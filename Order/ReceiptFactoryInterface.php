<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Order;

use Darvin\PaymentBundle\Entity\PaymentInterface;

/**
 * Payment status provider
 */
interface ReceiptFactoryInterface
{
    /**
     * @param PaymentInterface $payment
     *
     * @return array
     *
     * @throws \Darvin\PaymentBundle\Order\Exception\CantCreateReceiptException
     */
    public function createReceipt(PaymentInterface $payment): array;
}
