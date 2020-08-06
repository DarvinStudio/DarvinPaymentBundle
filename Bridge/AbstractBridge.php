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

abstract class AbstractBridge implements BridgeInterface
{
    /**
     * @var array
     */
    private $gatewayConfig = [];

    public function setGatewayConfig(array $gatewayConfig = [])
    {
        $this->gatewayConfig = $gatewayConfig;
    }

    /**
     * @return array
     */
    public function getGatewayConfig()
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
    public function completePurchaseParameters(PaymentInterface $payment): array
    {
        return $this->purchaseParameters($payment);
    }
}
