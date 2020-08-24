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

/**
 * Sberbank gateway parameters bridge
 */
class SberbankBridge extends AbstractBridge
{
    /**
     * @var ReceiptFactoryInterface|null
     */
    private $receiptFactory;

    /**
     * @param \Darvin\PaymentBundle\Order\ReceiptFactoryInterface $receiptFactory Order receipt factory
     */
    public function setReceiptFactory(ReceiptFactoryInterface $receiptFactory): void
    {
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
            'orderBundle'        => $this->getReceipt($payment),
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

    /**
     * @param \Darvin\PaymentBundle\Entity\PaymentInterface $payment Payment
     *
     * @return string|null
     */
    private function getReceipt(PaymentInterface $payment): ?string
    {
        if (null !== $this->receiptFactory) {
            try {
                return json_encode($this->receiptFactory->createReceipt($payment));
            } catch (\Darvin\PaymentBundle\Order\Exception\CantCreateReceiptException $ex) {
                // TODO Need add logger
                return null;
            }
        }

        return null;
    }
}
