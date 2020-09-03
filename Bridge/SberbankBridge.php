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

use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Receipt\ReceiptFactoryRegistryInterface;
use Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface;

/**
 * Sberbank gateway parameters bridge
 */
class SberbankBridge extends AbstractBridge
{
    /**
     * @var \Darvin\PaymentBundle\Receipt\ReceiptFactoryRegistryInterface|null
     */
    private $receiptFactoryRegistry;

    /**
     * @param ReceiptFactoryRegistryInterface $receiptFactoryRegistry Registry of receipt factories
     * @param PaymentUrlBuilderInterface      $urlBuilder             URL Builder
     */
    public function __construct(
        ReceiptFactoryRegistryInterface $receiptFactoryRegistry,
        PaymentUrlBuilderInterface $urlBuilder
    ) {
        parent::__construct($urlBuilder);
        $this->receiptFactoryRegistry = $receiptFactoryRegistry;
    }

    /**
     * @return string
     */
    public function getGatewayClassName(): string
    {
        return \Omnipay\Sberbank\SberbankGateway::class;
    }

    /**
     * @inheritDoc
     */
    public function authorizeParameters(Payment $payment): array
    {
        return [
            'orderNumber'        => $payment->getOrderId(),
            'amount'             => $payment->getAmount(),
            'currency'           => $payment->getCurrencyCode(),
            'description'        => $payment->getDescription(),
            'returnUrl'          => $this->urlBuilder->getCompleteAuthorizeUrl($payment, 'sberbank'),
            'failUrl'            => $this->urlBuilder->getFailUrl($payment, 'sberbank'),
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
    public function completeAuthorizeParameters(Payment $payment): array
    {
        return [
            'orderId'     => $payment->getTransactionReference(),
            'orderNumber' => $payment->getOrderId(),
            'amount'      => $payment->getAmount(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function purchaseParameters(Payment $payment): array
    {
        return [
            'orderNumber'        => $payment->getOrderId(),
            'amount'             => $payment->getAmount(),
            'currency'           => $payment->getCurrencyCode(),
            'description'        => $payment->getDescription(),
            'returnUrl'          => $this->urlBuilder->getCompletePurchaseUrl($payment, 'sberbank'),
            'failUrl'            => $this->urlBuilder->getFailUrl($payment, 'sberbank'),
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
    public function completePurchaseParameters(Payment $payment): array
    {
        return [
            'orderId'     => $payment->getTransactionReference(),
            'orderNumber' => $payment->getOrderId(),
            'amount'      => $payment->getAmount(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function captureParameters(Payment $payment): array
    {
        return [
            'orderId' => $payment->getTransactionReference(),
            'amount'  => $payment->getAmount(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function refundParameters(Payment $payment): array
    {
        return [
            'orderId' => $payment->getTransactionReference(),
            'amount'  => $payment->getAmount(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function acceptNotificationParameters(Payment $payment): array
    {
        // TODO: Implement acceptNotificationParameters() method.
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment Payment
     *
     * @return string|null
     */
    private function getReceipt(Payment $payment): ?string
    {
        if ($this->receiptFactoryRegistry->hasFactory($payment)) {
            $factory = $this->receiptFactoryRegistry->getFactory($payment);

            try {
                return json_encode($factory->createReceipt($payment));
            } catch (\Darvin\PaymentBundle\Receipt\Exception\CantCreateReceiptException $ex) {
                // TODO Need add logger
                return null;
            }
        }

        return null;
    }
}
