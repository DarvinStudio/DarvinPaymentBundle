<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Url;

use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Url\Exception\ActionNotImplementedException;

/**
 * Payment url builder interface
 */
interface PaymentUrlBuilderInterface
{
    /**
     * @param Payment $payment     Payment
     * @param string  $gatewayName Gateway name
     *
     * @return string
     * @throws \LogicException
     */
    public function getPurchaseUrl(Payment $payment, string $gatewayName): string;

    /**
     * @param Payment $payment Payment
     *
     * @return string
     * @throws \LogicException
     */
    public function getCompleteUrl(Payment $payment): string;

    /**
     * @param Payment $payment Payment
     *
     * @return string
     * @throws \LogicException
     */
    public function getCaptureUrl(Payment $payment): string;

    /**
     * @param Payment $payment Payment
     *
     * @return string
     * @throws \LogicException
     */
    public function getRefundUrl(Payment $payment): string;

    /**
     * @param Payment $payment Payment
     *
     * @return string
     */
    public function getNotifyUrl(Payment $payment): string;

    /**
     * @param Payment $payment Payment
     *
     * @return string
     * @throws \LogicException
     */
    public function getApproveUrl(Payment $payment): string;

    /**
     * @param Payment $payment Payment
     *
     * @return string
     * @throws \LogicException
     */
    public function getCancelUrl(Payment $payment): string;

    /**
     * @param Payment $payment Payment
     *
     * @return string
     * @throws \LogicException
     */
    public function getVoidUrl(Payment $payment): string;

    /**
     * @param Payment $payment Payment
     *
     * @return string
     * @throws \LogicException
     */
    public function getSuccessUrl(Payment $payment): string;

    /**
     * @param Payment $payment Payment
     *
     * @return string
     * @throws \LogicException
     */
    public function getFailUrl(Payment $payment): string;

    /**
     * @param Payment $payment Payment
     *
     * @return string
     * @throws \LogicException
     */
    public function getErrorUrl(Payment $payment): string;
}
