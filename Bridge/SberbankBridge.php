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

use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Receipt\Exception\CantCreateReceiptException;
use Darvin\PaymentBundle\Receipt\ReceiptFactoryRegistryInterface;
use Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface;
use Omnipay\Sberbank\SberbankGateway;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @param \Darvin\PaymentBundle\Receipt\ReceiptFactoryRegistryInterface $receiptFactoryRegistry Registry of receipt factories
     * @param \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface          $urlBuilder             URL Builder
     * @param \Psr\Log\LoggerInterface                                      $logger                 Payment logger
     * @param \Symfony\Contracts\Translation\TranslatorInterface            $translator             Translator
     */
    public function __construct(
        ReceiptFactoryRegistryInterface $receiptFactoryRegistry,
        PaymentUrlBuilderInterface $urlBuilder,
        LoggerInterface $logger,
        TranslatorInterface $translator
    ) {
        parent::__construct($urlBuilder);

        $this->receiptFactoryRegistry = $receiptFactoryRegistry;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function getGatewayClassName(): string
    {
        return SberbankGateway::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getGatewayName(): string
    {
        return 'sberbank';
    }

    /**
     * {@inheritDoc}
     */
    public function getSessionTimeout(): int
    {
        return $this->getGatewayParameter('sessionTimeoutSecs', 86400);
    }

    /**
     * {@inheritDoc}
     */
    public function authorizeParameters(Payment $payment): array
    {
        return $this->purchaseParameters($payment);
    }

    /**
     * {@inheritDoc}
     */
    public function completeAuthorizeParameters(Payment $payment): array
    {
        return [
            'orderId'     => $payment->getTransactionReference(),
            'orderNumber' => $payment->getOrder()->getId(),
            'amount'      => $payment->getAmount(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function purchaseParameters(Payment $payment): array
    {
        return [
            'orderNumber'        => $payment->getId(),
            'amount'             => $payment->getAmount(),
            'currency'           => $payment->getCurrency(),
            'description'        => $payment->getDescription(),
            'returnUrl'          => $this->urlBuilder->getCompleteUrl($payment),
            'failUrl'            => $this->urlBuilder->getFailUrl($payment),
            'jsonParams'         => json_encode(['Order ID' => $payment->getOrder()->getNumber()]),
            'sessionTimeoutSecs' => $this->getSessionTimeout(),
            'clientId'           => $payment->getClient()->getId(),
            'email'              => $payment->getClient()->getEmail(),
            'taxSystem'          => $this->getGatewayParameter('taxSystem'),
            'orderBundle'        => $this->getReceipt($payment),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function completePurchaseParameters(Payment $payment): array
    {
        return [
            'orderId'     => $payment->getTransactionReference(),
            'orderNumber' => $payment->getOrder()->getId(),
            'amount'      => $payment->getAmount(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function voidParameters(Payment $payment): array
    {
        return [
            'orderId' => $payment->getTransactionReference(),
            'amount'  => $payment->getAmount(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function captureParameters(Payment $payment): array
    {
        return [
            'orderId' => $payment->getTransactionReference(),
            'amount'  => $payment->getAmount(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function refundParameters(Payment $payment): array
    {
        return [
            'orderId' => $payment->getTransactionReference(),
            'amount'  => $payment->getAmount(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function acceptNotificationParameters(Payment $payment): array
    {
        // TODO: Implement acceptNotificationParameters() method.
        throw new \RuntimeException('Not implemented.');
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment Payment
     *
     * @return string|null
     */
    private function getReceipt(Payment $payment): ?string
    {
        if (!$this->receiptFactoryRegistry->hasFactory($payment, $this->getGatewayName())) {
            return null;
        }

        $factory = $this->receiptFactoryRegistry->getFactory($payment, $this->getGatewayName());

        try {
            return json_encode($factory->createReceipt($payment, $this->getGatewayName()));
        } catch (CantCreateReceiptException $ex) {
            $this->logger->warning(
                $this->translator->trans('error.cant_create_receipt', [], 'payment_event'),
                [
                    'payment' => $payment,
                ]
            );
        }

        return null;
    }
}
