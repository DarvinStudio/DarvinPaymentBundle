<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.07.2018
 * Time: 21:00
 */

namespace Darvin\PaymentBundle\Manager;

use Darvin\PaymentBundle\Entity\PaymentInterface;

/**
 * Interface PaymentManagerInterface
 * @package Darvin\PaymentBundle\Manager
 */
interface PaymentManagerInterface
{
    /**
     * @param int             $orderId
     * @param string          $orderEntityClass
     * @param string          $amount
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
        int $orderId,
        string $orderEntityClass,
        string $amount,
        string $currencyCode,
        $clientId,
        ?string $clientEmail,
        ?string $description,
        ?array $options
    );

    /**
     * @param PaymentInterface $payment
     *
     * @return void
     */
    public function markAsNew(PaymentInterface $payment): void;

    /**
     * @param PaymentInterface $payment
     *
     * @return void
     */
    public function markAsPending(PaymentInterface $payment): void;

    /**
     * @param PaymentInterface $payment
     *
     * @return void
     */
    public function markAsPaid(PaymentInterface $payment): void;

    /**
     * @param PaymentInterface $payment
     *
     * @return void
     */
    public function markAsCanceled(PaymentInterface $payment): void;

    /**
     * @param PaymentInterface $payment
     *
     * @return void
     */
    public function markAsFailed(PaymentInterface $payment): void;

    /**
     * @param PaymentInterface $payment
     *
     * @return void
     */
    public function markAsRefund(PaymentInterface $payment): void;

    /**
     * @param PaymentInterface $payment
     * @param string           $status
     *
     * @return void
     */
    public function markAs(PaymentInterface $payment, string $status): void;

    /**
     * @param PaymentInterface $payment
     * @param string           $reference
     *
     * @return void
     */
    public function setTransactionReference(PaymentInterface $payment, string $reference): void;

    /**
     * @param int $id
     *
     * @return PaymentInterface|null
     */
    public function findById(int $id): ?PaymentInterface;
}
