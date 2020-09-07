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
use Darvin\PaymentBundle\Logger\PaymentLoggerInterface;
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
     * @var \Darvin\PaymentBundle\Logger\PaymentLoggerInterface
     */
    private $logger;

    /**
     * @param \Darvin\PaymentBundle\Receipt\ReceiptFactoryRegistryInterface $receiptFactoryRegistry Registry of receipt factories
     * @param \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface          $urlBuilder             URL Builder
     * @param \Darvin\PaymentBundle\Logger\PaymentLoggerInterface           $logger                 Payment logger
     */
    public function __construct(
        ReceiptFactoryRegistryInterface $receiptFactoryRegistry,
        PaymentUrlBuilderInterface $urlBuilder,
        PaymentLoggerInterface $logger
    ) {
        parent::__construct($urlBuilder);
        $this->receiptFactoryRegistry = $receiptFactoryRegistry;
        $this->logger = $logger;
    }

    /**
     * @return string
     */
    public function getGatewayClassName(): string
    {
        return \Omnipay\Sberbank\SberbankGateway::class;
    }

    /**
     * @return int
     */
    public function getSessionTimeout(): int
    {
        return $this->getGatewayConfig()['sessionTimeoutSecs'] ?? 86400;
    }

    /**
     * @inheritDoc
     */
    public function authorizeParameters(Payment $payment): array
    {
        return [
            'orderNumber'        => $payment->getId(),
            'amount'             => $payment->getAmount(),
            'currency'           => $payment->getCurrencyCode(),
            'description'        => $payment->getDescription(),
            'returnUrl'          => $this->urlBuilder->getCompleteAuthorizeUrl($payment),
            'failUrl'            => $this->urlBuilder->getFailUrl($payment),
            'jsonParams'         => json_encode(['Order ID' => $payment->getId()]),
            'sessionTimeoutSecs' => $this->getSessionTimeout(),
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
            'orderNumber'        => $payment->getId(),
            'amount'             => $payment->getAmount(),
            'currency'           => $payment->getCurrencyCode(),
            'description'        => $payment->getDescription(),
            'returnUrl'          => $this->urlBuilder->getCompletePurchaseUrl($payment),
            'failUrl'            => $this->urlBuilder->getFailUrl($payment),
            'jsonParams'         => json_encode(['Order ID' => $payment->getId()]),
            'sessionTimeoutSecs' => $this->getSessionTimeout(),
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

                $this->logger->saveErrorLog($payment, null, $ex->getMessage());

                return null;
            }
        }

        return null;
    }
}
