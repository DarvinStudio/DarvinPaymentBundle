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

use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\Order\ReceiptBuilderInterface;
use Darvin\PaymentBundle\UrlBuilder\PaymentUrlBuilderInterface;

/**
 * Sberbank gateway parameters bridge
 */
class SberbankBridge extends AbstractBridge
{
    /**
     * @var ReceiptBuilderInterface
     */
    private $receiptBuilder;

    /**
     * @param PaymentUrlBuilderInterface $urlBuilder
     * @param ReceiptBuilderInterface    $receiptBuilder
     */
    public function __construct(
        PaymentUrlBuilderInterface $urlBuilder,
        ReceiptBuilderInterface $receiptBuilder
    ) {
        parent::__construct($urlBuilder);
        $this->receiptBuilder = $receiptBuilder;
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
    public function authorizationParameters(PaymentInterface $payment): array
    {
        return [
            'orderNumber'        => $payment->getOrderId(),
            'amount'             => $payment->getAmount(),
            'description'        => $payment->getDescription(),
            'returnUrl'          => $this->urlBuilder->getSuccessUrl($payment, 'sberbank'),
            'failUrl'            => $this->urlBuilder->getFailedUrl($payment, 'sberbank'),
            'sessionTimeoutSecs' => $this->getGatewayConfig()['sessionTimeoutSecs'] ?? 28800,
            'taxSystem'          => $this->getGatewayConfig()['taxSystem'] ?? null,
            'orderBundle'        => $this->receiptBuilder->createReceipt($payment),
        ];
    }

    /**
     * @inheritDoc
     */
    public function captureParameters(PaymentInterface $payment): array
    {
        return [];
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
