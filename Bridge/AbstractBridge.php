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
use Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface;

/**
 * Abstract bridge
 */
abstract class AbstractBridge implements BridgeInterface
{
    /**
     * @var \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface
     */
    protected $urlBuilder;

    /**
     * @var array
     */
    private $gatewayConfig;

    /**
     * @param \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface $urlBuilder URL Builder
     */
    public function __construct(PaymentUrlBuilderInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
        $this->gatewayConfig = [];
    }

    public function setGatewayConfig(array $gatewayConfig = []): void
    {
        $this->gatewayConfig = $gatewayConfig;
    }

    /**
     * @return array
     */
    public function getGatewayConfig(): array
    {
        return $this->gatewayConfig;
    }

    /**
     * @inheritDoc
     */
    public function initializationParameters(): array
    {
        return $this->getGatewayConfig();
    }

    /**
     * @inheritDoc
     */
    public function completePurchaseParameters(Payment $payment): array
    {
        return $this->purchaseParameters($payment);
    }
}
