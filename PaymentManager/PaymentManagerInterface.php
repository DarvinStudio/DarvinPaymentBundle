<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.07.2018
 * Time: 21:00
 */

namespace Darvin\PaymentBundle\PaymentManager;

use Darvin\PaymentBundle\Entity\PaymentInterface;

/**
 * Interface PaymentManagerInterface
 * @package Darvin\PaymentBundle\PaymentManager
 */
interface PaymentManagerInterface
{
    /**
     * @param int             $orderId
     * @param string          $orderEntityClass
     * @param float           $amount
     * @param string          $currencyCode
     * @param int|null|string $clientId
     * @param null|string     $clientEmail
     * @param null|string     $description
     *
     * @param array           $options
     *
     * @return PaymentInterface
     */
    public function create(
        $orderId,
        $orderEntityClass,
        $amount,
        $currencyCode,
        $clientId = null,
        $clientEmail = null,
        $description = null,
        array $options = []
    );

    /**
     * @param PaymentInterface $payment
     *
     * @return void
     */
    public function markAsNew(PaymentInterface $payment);

    /**
     * @param PaymentInterface $payment
     *
     * @return void
     */
    public function markAsPending(PaymentInterface $payment);

    /**
     * @param PaymentInterface $payment
     *
     * @return void
     */
    public function markAsPaid(PaymentInterface $payment);

    /**
     * @param PaymentInterface $payment
     *
     * @return void
     */
    public function markAsCanceled(PaymentInterface $payment);

    /**
     * @param PaymentInterface $payment
     *
     * @return void
     */
    public function markAsFailed(PaymentInterface $payment);

    /**
     * @param PaymentInterface $payment
     *
     * @return void
     */
    public function markAsRefund(PaymentInterface $payment);

    /**
     * @param PaymentInterface $payment
     * @param string           $status
     *
     * @return void
     */
    public function markAs(PaymentInterface $payment, $status);

    /**
     * @param PaymentInterface $payment
     * @param string|null      $reference
     *
     * @return void
     */
    public function setTransactionReference(PaymentInterface $payment, $reference);

    /**
     * @param int $id
     *
     * @return PaymentInterface|null
     */
    public function findById($id);

}
