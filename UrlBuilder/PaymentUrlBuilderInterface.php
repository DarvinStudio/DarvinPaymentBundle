<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 07.07.2018
 * Time: 1:52
 */

namespace Darvin\PaymentBundle\UrlBuilder;


use Darvin\PaymentBundle\Entity\PaymentInterface;

/**
 * Interface PaymentUrlBuilderInterface
 * @package Darvin\PaymentBundle\UrlBuilder
 */
interface PaymentUrlBuilderInterface
{
    /**
     * @param PaymentInterface $payment
     * @param null|string      $gateway
     *
     * @return string
     */
    public function getAuthorizationUrl(PaymentInterface $payment, $gateway = null);

    /**
     * @param PaymentInterface $payment
     * @param null|string      $gateway
     *
     * @return string
     */
    public function getCaptureUrl(PaymentInterface $payment, $gateway = null);

    /**
     * @param PaymentInterface $payment
     * @param null|string      $gateway
     *
     * @return string
     */
    public function getPurchaseUrl(PaymentInterface $payment, $gateway = null);

    /**
     * @param PaymentInterface $payment
     * @param null|string      $gateway
     *
     * @param string           $action
     *
     * @return string
     */
    public function getSuccessUrl(PaymentInterface $payment, $gateway = null, $action = 'purchase');

    /**
     * @param PaymentInterface $payment
     * @param null|string      $gateway
     *
     * @param string           $action
     *
     * @return string
     */
    public function getCanceledUrl(PaymentInterface $payment, $gateway = null, $action = 'purchase');

    /**
     * @param PaymentInterface $payment
     * @param null|string      $gateway
     *
     * @param string           $action
     *
     * @return string
     */
    public function getFailedUrl(PaymentInterface $payment, $gateway = null, $action = 'purchase');

    /**
     * @param PaymentInterface $payment
     * @param null|string      $gateway
     *
     * @return string
     */
    public function getRefundUrl(PaymentInterface $payment, $gateway = null);

    /**
     * @param PaymentInterface $payment
     * @param null|string      $gateway
     *
     * @return string
     */
    public function getNotifyUrl(PaymentInterface $payment, $gateway = null);
}
