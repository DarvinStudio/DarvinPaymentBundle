<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\UrlBuilder;

use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\UrlBuilder\Exception\ActionNotImplementedException;

/**
 * Payment url builder interface
 */
interface PaymentUrlBuilderInterface
{
    /**
     * @param PaymentInterface $payment     Payment
     * @param string           $gatewayName Gateway name
     *
     * @return string
     *
     * @throws ActionNotImplementedException
     */
    public function getAuthorizationUrl(PaymentInterface $payment, string $gatewayName): string;

    /**
     * @param PaymentInterface $payment     Payment
     * @param string           $gatewayName Gateway name
     *
     * @return string
     *
     * @throws ActionNotImplementedException
     */
    public function getCaptureUrl(PaymentInterface $payment, string $gatewayName): string;

    /**
     * @param PaymentInterface $payment     Payment
     * @param string           $gatewayName Gateway name
     *
     * @return string
     *
     * @throws ActionNotImplementedException
     */
    public function getPurchaseUrl(PaymentInterface $payment, string $gatewayName): string;

    /**
     * @param PaymentInterface $payment     Payment
     * @param string           $gatewayName Gateway name
     *
     * @return string
     *
     * @throws ActionNotImplementedException
     */
    public function getSuccessUrl(PaymentInterface $payment, string $gatewayName): string;

    /**
     * @param PaymentInterface $payment     Payment
     * @param string           $gatewayName Gateway name
     *
     * @return string
     *
     * @throws ActionNotImplementedException
     */
    public function getCanceledUrl(PaymentInterface $payment, string $gatewayName): string;

    /**
     * @param PaymentInterface $payment     Payment
     * @param string           $gatewayName Gateway name
     *
     * @return string
     *
     * @throws ActionNotImplementedException
     */
    public function getFailedUrl(PaymentInterface $payment, string $gatewayName): string;

    /**
     * @param PaymentInterface $payment     Payment
     * @param string           $gatewayName Gateway name
     *
     * @return string
     *
     * @throws ActionNotImplementedException
     */
    public function getRefundUrl(PaymentInterface $payment, string $gatewayName): string;

    /**
     * @param PaymentInterface $payment     Payment
     * @param string           $gatewayName Gateway name
     *
     * @return string
     *
     * @throws ActionNotImplementedException
     */
    public function getNotifyUrl(PaymentInterface $payment, string $gatewayName): string;
}
