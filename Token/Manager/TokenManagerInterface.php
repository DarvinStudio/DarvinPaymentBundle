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
 * Interface TokenManagerInterface
 * @package Darvin\PaymentBundle\Token\Manager
 */
interface TokenManagerInterface
{
    /**
     * @param PaymentInterface $payment
     *
     * @return void
     */
    public function createActionToken(PaymentInterface $payment);

    /**
     * @param PaymentInterface $payment
     *
     * @return void
     */
    public function invalidateActionToken(PaymentInterface $payment);

    /**
     * @param $token
     *
     * @return PaymentInterface|null
     */
    public function findPayment($token);
}
