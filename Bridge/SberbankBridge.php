<?php
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Bridge;

use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\Order\ReceiptFactoryInterface;
use Darvin\PaymentBundle\UrlBuilder\PaymentUrlBuilderInterface;

/**
 * Sberbank gateway parameters bridge
 */
class SberbankBridge extends AbstractBridge
{
    /**
     * @var ReceiptFactoryInterface
     */
    private $receiptFactory;

    /**
     * @param \Darvin\PaymentBundle\UrlBuilder\PaymentUrlBuilderInterface $urlBuilder
     * @param \Darvin\PaymentBundle\Order\ReceiptFactoryInterface         $receiptFactory
     */
    public function __construct(
        PaymentUrlBuilderInterface $urlBuilder,
        ReceiptFactoryInterface $receiptFactory
    ) {
        parent::__construct($urlBuilder);
        $this->receiptFactory = $receiptFactory;
    }

    /**
     * @return string
     */
    public function getGatewayClassName(): string
    {
        return \Darvin\Omnipay\Sberbank\SberbankGateway::class;
    }

    /**
     * @inheritDoc
     */
    public function authorizationParameters(PaymentInterface $payment): array
    {
        return [
            'orderNumber'        => $payment->getOrderId(),
            'amount'             => $payment->getAmount(),
            'description'        => $payment->getDescription(),
            'returnUrl'          => $this->urlBuilder->getSuccessUrl($payment, 'sberbank'),
            'failUrl'            => $this->urlBuilder->getFailedUrl($payment, 'sberbank'),
            'sessionTimeoutSecs' => $this->getGatewayConfig()['sessionTimeoutSecs'] ?? 28800,
            'clientId'           => $payment->getClientId(),
            'email'              => $payment->getClientEmail(),
            'taxSystem'          => $this->getGatewayConfig()['taxSystem'] ?? null,
            'orderBundle'        => json_encode($this->receiptFactory->createReceipt($payment)),
        ];
    }

    /**
     * @inheritDoc
     */
    public function completeAuthorizationParameters(PaymentInterface $payment): array
    {
        return [
            'orderId' => $payment->getTransactionRef(),
            'amount'  => $payment->getAmount(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function purchaseParameters(PaymentInterface $payment): array
    {
        return $this->authorizationParameters($payment);
    }

    /**
     * @inheritDoc
     */
    public function completePurchaseParameters(PaymentInterface $payment): array
    {
        return $this->completeAuthorizationParameters($payment);
    }

    /**
     * @inheritDoc
     */
    public function captureParameters(PaymentInterface $payment): array
    {
        return [
            'orderId' => $payment->getTransactionRef(),
            'amount'  => $payment->getAmount(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function refundParameters(PaymentInterface $payment): array
    {
        return [
            'orderId' => $payment->getTransactionRef(),
            'amount'  => $payment->getAmount(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function acceptNotificationParameters(PaymentInterface $payment): array
    {
        // TODO: Implement acceptNotificationParameters() method.
    }
}
