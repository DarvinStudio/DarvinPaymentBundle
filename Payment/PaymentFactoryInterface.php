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

use Darvin\PaymentBundle\Entity\Client;
use Darvin\PaymentBundle\Entity\PaidOrder;
use Darvin\PaymentBundle\Entity\Payment;

/**
 * Payment factory interface
 */
interface PaymentFactoryInterface
{
    /**
     * @param \Darvin\PaymentBundle\Entity\PaidOrder $order    Order
     * @param \Darvin\PaymentBundle\Entity\Client    $client   Client
     * @param string                                 $amount   Amount
     * @param string|null                            $currency Currency code
     *
     * @return \Darvin\PaymentBundle\Entity\Payment
     *
     * @throws \InvalidArgumentException
     */
    public function createPayment(
        PaidOrder $order,
        Client $client,
        string $amount,
        ?string $currency = null
    ): Payment;
}
