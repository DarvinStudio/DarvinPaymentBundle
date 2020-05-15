<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 07.07.2018
 * Time: 1:52
 */

namespace Darvin\PaymentBundle\UrlBuilder;


use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\UrlBuilder\Exception\ActionNotImplementedException;

/**
 * Interface PaymentUrlBuilderInterface
 * @package Darvin\PaymentBundle\UrlBuilder
 */
interface PaymentUrlBuilderInterface
{
    /**
     * @param PaymentInterface $payment
     * @param string           $gatewayName
     *
     * @return string
     *
     * @throws ActionNotImplementedException
     */
    public function getAuthorizationUrl(PaymentInterface $payment, string $gatewayName): string;

    /**
     * @param PaymentInterface $payment
     * @param string           $gatewayName
     *
     * @return string
     *
     * @throws ActionNotImplementedException
     */
    public function getCaptureUrl(PaymentInterface $payment, string $gatewayName): string;

    /**
     * @param PaymentInterface $payment
     * @param string           $gatewayName
     *
     * @return string
     *
     * @throws ActionNotImplementedException
     */
    public function getPurchaseUrl(PaymentInterface $payment, string $gatewayName): string;

    /**
     * @param PaymentInterface $payment
     * @param string           $gatewayName
     * @param string           $action
     *
     * @return string
     *
     * @throws ActionNotImplementedException
     */
    public function getSuccessUrl(PaymentInterface $payment, string $gatewayName, string $action = 'purchase'): string;

    /**
     * @param PaymentInterface $payment
     * @param string           $gatewayName
     * @param string           $action
     *
     * @return string
     */
    public function getCanceledUrl(PaymentInterface $payment, string $gatewayName, string $action = 'purchase'): string;

    /**
     * @param PaymentInterface $payment
     * @param string           $gatewayName
     * @param string           $action
     *
     * @return string
     *
     * @throws ActionNotImplementedException
     */
    public function getFailedUrl(PaymentInterface $payment, string $gatewayName, string $action = 'purchase'): string;

    /**
     * @param PaymentInterface $payment
     * @param string           $gatewayName
     *
     * @return string
     *
     * @throws ActionNotImplementedException
     */
    public function getRefundUrl(PaymentInterface $payment, string $gatewayName): string;

    /**
     * @param PaymentInterface $payment
     * @param string           $gatewayName
     *
     * @return string
     *
     * @throws ActionNotImplementedException
     */
    public function getNotifyUrl(PaymentInterface $payment, string $gatewayName): string;
}
