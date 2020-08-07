<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Bridge;

use Darvin\OmnipayTelr\TelrGateway;
use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\UrlBuilder\PaymentUrlBuilderInterface;

class TelrBridge extends AbstractBridge
{
    /**
     * @var PaymentUrlBuilderInterface
     */
    protected $urlBuilder;

    /**
     * TelrBridge constructor.
     *
     * @param PaymentUrlBuilderInterface $urlBuilder
     */
    public function __construct(PaymentUrlBuilderInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritDoc
     */
    public function getGatewayClassName(): string
    {
        return TelrGateway::class;
    }

    /**
     * @inheritDoc
     */
    public function authorizationParameters(PaymentInterface $payment): array
    {
        // TODO: Implement authorizationParameters() method.
    }

    /**
     * @inheritDoc
     */
    public function captureParameters(PaymentInterface $payment): array
    {
        // TODO: Implement captureParameters() method.
    }

    /**
     * @inheritDoc
     */
    public function purchaseParameters(PaymentInterface $payment): array
    {
        return [
            'ivp_amount'   => $payment->getAmount(),
            'ivp_currency' => $payment->getCurrencyCode(),
            'ivp_cart'     => $payment->getOrderId(),
            'ivp_desc'     => $payment->getDescription(),
            'return_auth'  => $this->urlBuilder->getSuccessUrl($payment, 'telr'),
            'return_decl'  => $this->urlBuilder->getFailedUrl($payment, 'telr'),
            'return_can'   => $this->urlBuilder->getCanceledUrl($payment, 'telr')
        ];
    }

    /**
     * @inheritDoc
     */
    public function completePurchaseParameters(PaymentInterface $payment): array
    {
        return [
            'order_ref' => $payment->getTransactionRef()
        ];
    }


    /**
     * @inheritDoc
     */
    public function refundParameters(PaymentInterface $payment): array
    {
        // TODO: Implement refundParameters() method.
    }

    /**
     * @inheritDoc
     */
    public function acceptNotificationParameters(PaymentInterface $payment): array
    {
        // TODO: Implement acceptNotificationParameters() method.
    }
}
