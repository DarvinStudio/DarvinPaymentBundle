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

use Darvin\PaymentBundle\Entity\PaymentInterface;

/**
 * Interface PaymentTokenManagerInterface
 * @package Darvin\PaymentBundle\Token\Manager
 */
interface PaymentTokenManagerInterface
{
    /**
     * @param PaymentInterface $payment
     *
     * @return void
     */
    public function attach(PaymentInterface $payment): void;

    /**
     * @param PaymentInterface $payment
     *
     * @return void
     */
    public function invalidate(PaymentInterface $payment): void;

    /**
     * @param $token
     *
     * @return PaymentInterface|null
     */
    public function findPayment($token): ?PaymentInterface;
}
