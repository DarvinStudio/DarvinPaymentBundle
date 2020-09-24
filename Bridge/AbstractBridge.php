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
     * @param \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface $urlBuilder URL Builder
     */
    public function __construct(PaymentUrlBuilderInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;

        $this->gatewayConfig = [];
    }

    /**
     * @param array $gatewayConfig Gateway config
     */
    public function setGatewayConfig(array $gatewayConfig): void
    {
        $this->gatewayConfig = $gatewayConfig;
    }

    /**
     * @param string $name    Parameter name
     * @param mixed  $default Default value
     *
     * @return mixed
     */
    public function getGatewayParameter(string $name, $default = null)
    {
        return $this->gatewayConfig[$name] ?? $default;
    }

    /**
     * {@inheritDoc}
     */
    public function initializationParameters(): array
    {
        return $this->gatewayConfig;
    }
}
