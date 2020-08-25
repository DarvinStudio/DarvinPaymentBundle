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
use Darvin\PaymentBundle\Receipt\ReceiptFactoryRegistryInterface;
use Darvin\PaymentBundle\UrlBuilder\PaymentUrlBuilderInterface;

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
        if ($this->receiptFactoryRegistry->hasFactory($payment)) {
            try {
                $factory = $this->receiptFactoryRegistry->getFactory($payment);
            } catch (\Darvin\PaymentBundle\Receipt\Exception\FactoryNotExistException $ex) {
                // TODO Need add logger
                return null;
            }

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
