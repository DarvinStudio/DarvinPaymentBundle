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
    protected $gatewayConfig;

    /**
     * @var array
     */
    protected $receiptConfig;

    /**
     * @param \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface $urlBuilder URL Builder
     */
    public function __construct(PaymentUrlBuilderInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;

        $this->gatewayConfig = [];
    }

    /**
     * {@inheritDoc}
     */
    public function initializationParameters(): array
    {
        return $this->gatewayConfig;
    }

    /**
     * @param array $gatewayConfig Gateway config
     */
    public function setGatewayConfig(array $gatewayConfig): void
    {
        $this->gatewayConfig = $gatewayConfig;
    }

    /**
     * @param array $receiptConfig Receipt config
     */
    public function setReceiptConfig(array $receiptConfig): void
    {
        $this->receiptConfig = $receiptConfig;
    }

    /**
     * {@inheritDoc}
     */
    public function getGatewayParameter(string $name, $default = null)
    {
        return $this->gatewayConfig[$name] ?? $default;
    }

    /**
     * {@inheritDoc}
     */
    public function getReceiptParameter(string $name, $default = null)
    {
        return $this->receiptConfig[$name] ?? $default;
    }
}
