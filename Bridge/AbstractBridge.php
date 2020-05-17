<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 07.07.2018
 * Time: 0:24
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
