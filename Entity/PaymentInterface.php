<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.07.2018
 * Time: 17:00
 */

namespace Darvin\PaymentBundle\Entity;


interface PaymentInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @return string Request or Order class name
     */
    public function getOrderEntityClass();

    /**
     * @return string|null
     */
    public function getTransactionRef();

    /**
     * @param $reference
     *
     * @return void
     */
    public function setTransactionRef($reference);

    /**
     * @return double
     */
    public function getAmount();

    /**
     * @return string Currency code in ISO format (USD, RUB, AED...)
     */
    public function getCurrencyCode();

    /**
     * @return string|int|null
     */
    public function getClientId();

    /**
     * @return string|null
     */
    public function getClientEmail();

    /**
     * @return string|null
     */
    public function getDescription();

    /**
     * @return string One of PaymentStatusType constant
     */
    public function getStatus();

    /**
     * @param string $status One of PaymentStatusType constant
     *
     * @return void
     */
    public function setStatus($status);

    /**
     * @return bool
     */
    public function isPaid();

    /**
     * @return string|null
     */
    public function getActionToken();

    /**
     * @param string|null $token
     *
     * @return void
     */
    public function setActionToken($token);
}