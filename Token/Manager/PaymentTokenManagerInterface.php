<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Token\Manager;

use Darvin\PaymentBundle\Entity\Payment;

/**
 * Payment token manager interface
 */
interface PaymentTokenManagerInterface
{
    /**
     * @param Payment $payment Payment
     *
     * @return void
     */
    public function attach(Payment $payment): void;

    /**
     * @param Payment $payment Payment
     *
     * @return void
     */
    public function invalidate(Payment $payment): void;
}
