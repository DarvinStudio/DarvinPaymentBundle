<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 08.07.2018
 * Time: 2:37
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
