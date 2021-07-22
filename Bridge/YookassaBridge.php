<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2021, Darvin Studio
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
use Omnipay\YooKassa\Gateway;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * YooKassa bridge
 */
class YookassaBridge extends AbstractBridge
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Darvin\PaymentBundle\Receipt\ReceiptFactoryRegistryInterface
     */
    private $receiptFactoryRegistry;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @param \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface          $urlBuilder
     * @param \Psr\Log\LoggerInterface                                      $logger
     * @param \Darvin\PaymentBundle\Receipt\ReceiptFactoryRegistryInterface $receiptFactoryRegistry
     * @param \Symfony\Contracts\Translation\TranslatorInterface            $translator
     */
    public function __construct(
        PaymentUrlBuilderInterface $urlBuilder,
        LoggerInterface $logger,
        ReceiptFactoryRegistryInterface $receiptFactoryRegistry,
        TranslatorInterface $translator
    ) {
        parent::__construct($urlBuilder);

        $this->logger = $logger;
        $this->receiptFactoryRegistry = $receiptFactoryRegistry;
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function getGatewayClassName(): string
    {
        return Gateway::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getGatewayName(): string
    {
        return 'yookassa';
    }

    /**
     * {@inheritDoc}
     */
    public function getSessionTimeout(): int
    {
        return $this->getGatewayParameter('sessionTimeoutSecs', 60 * 60 * 24 * 7);
    }

    /**
     * {@inheritDoc}
     */
    public function authorizeParameters(Payment $payment): array
    {
        throw new \RuntimeException('Not implemented.');
    }

    /**
     * {@inheritDoc}
     */
    public function completeAuthorizeParameters(Payment $payment): array
    {
        throw new \RuntimeException('Not implemented.');
    }

    /**
     * {@inheritDoc}
     */
    public function captureParameters(Payment $payment): array
    {
        throw new \RuntimeException('Not implemented.');
    }

    /**
     * {@inheritDoc}
     */
    public function purchaseParameters(Payment $payment): array
    {
        return [
            'amount'        => $payment->getAmount(),
            'currency'      => $payment->getCurrency(),
            'returnUrl'     => $this->urlBuilder->getCompleteUrl($payment),
            'transactionId' => implode('x', [$payment->getOrder()->getNumber(), $payment->getId()]),
            'description'   => (string)$payment->getDescription(),
            'capture'       => true,
            'receipt'       => $this->getReceipt($payment),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function completePurchaseParameters(Payment $payment): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function voidParameters(Payment $payment): array
    {
        throw new \RuntimeException('Not implemented.');
    }

    /**
     * {@inheritDoc}
     */
    public function refundParameters(Payment $payment): array
    {
        throw new \RuntimeException('Not implemented.');
    }

    /**
     * {@inheritDoc}
     */
    public function acceptNotificationParameters(Payment $payment): array
    {
        throw new \RuntimeException('Not implemented.');
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment
     *
     * @return array
     */
    private function getReceipt(Payment $payment): array
    {
        if (!$this->receiptFactoryRegistry->hasFactory($payment, $this)) {
            return [];
        }

        $factory = $this->receiptFactoryRegistry->getFactory($payment, $this);

        try {
            return $factory->createReceipt($payment, $this);
        } catch (CantCreateReceiptException $ex) {
            $this->logger->warning(
                $this->translator->trans('error.cant_create_receipt', [], 'payment_event'),
                [
                    'payment' => $payment,
                ]
            );
        }

        return [];
    }
}
