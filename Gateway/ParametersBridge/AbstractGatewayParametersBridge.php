<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 07.07.2018
 * Time: 0:24
 */

namespace Darvin\PaymentBundle\Gateway\ParametersBridge;

use Darvin\PaymentBundle\Entity\PaymentInterface;

abstract class AbstractGatewayParametersBridge implements GatewayParametersBridgeInterface
{
    /**
     * @var array
     */
    private $gatewayConfig = [];

    public function setGatewayConfig(array $config = [])
    {
        $this->gatewayConfig = $config;
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
