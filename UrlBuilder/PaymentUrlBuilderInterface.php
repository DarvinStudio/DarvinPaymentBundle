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

use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\UrlBuilder\Exception\ActionNotImplementedException;

/**
 * Payment url builder interface
 */
interface PaymentUrlBuilderInterface
{
    /**
     * @param Payment $payment     Payment
     * @param string           $gatewayName Gateway name
     *
     * @return string
     */
    public function getAuthorizeUrl(Payment $payment, string $gatewayName): string;

    /**
     * @param Payment $payment     Payment
     * @param string           $gatewayName Gateway name
     *
     * @return string
     *
     * @throws ActionNotImplementedException
     */
    public function getCaptureUrl(Payment $payment, string $gatewayName): string;

    /**
     * @param Payment $payment     Payment
     * @param string           $gatewayName Gateway name
     *
     * @return string
     */
    public function getPurchaseUrl(Payment $payment, string $gatewayName): string;

    /**
     * @param Payment $payment     Payment
     * @param string           $gatewayName Gateway name
     *
     * @return string
     */
    public function getSuccessUrl(Payment $payment, string $gatewayName): string;

    /**
     * @param Payment $payment     Payment
     * @param string           $gatewayName Gateway name
     *
     * @return string
     */
    public function getCanceledUrl(Payment $payment, string $gatewayName): string;

    /**
     * @param Payment $payment     Payment
     * @param string           $gatewayName Gateway name
     *
     * @return string
     */
    public function getFailedUrl(Payment $payment, string $gatewayName): string;

    /**
     * @param Payment $payment     Payment
     * @param string           $gatewayName Gateway name
     *
     * @return string
     *
     * @throws ActionNotImplementedException
     */
    public function getRefundUrl(Payment $payment, string $gatewayName): string;

    /**
     * @param Payment $payment     Payment
     * @param string           $gatewayName Gateway name
     *
     * @return string
     *
     * @throws ActionNotImplementedException
     */
    public function getNotifyUrl(Payment $payment, string $gatewayName): string;
}
