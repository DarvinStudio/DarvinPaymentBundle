<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.07.2018
 * Time: 23:54
 */

namespace Darvin\PaymentBundle\Gateway\Factory;

use Darvin\PaymentBundle\Bridge\BridgeInterface;
use Darvin\PaymentBundle\Bridge\Exception\BridgeNotSetException;
use Omnipay\Common\GatewayInterface;
use Omnipay\Omnipay;
use Symfony\Component\HttpFoundation\RequestStack;

class GatewayFactory implements GatewayFactoryInterface
{
    /**
     * @var array|BridgeInterface[]
     */
    protected $bridges = [];

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * GatewayFactory constructor.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @inheritDoc
     */
    public function createGateway($name): GatewayInterface
    {
        $bridge = $this->getBridge($name);
        $className = $bridge->getGatewayClassName();
        if ($className[0] !== '\\') {
            $className = '\\'.$className;
        }

        $gateway = Omnipay::create($className, null, $this->requestStack->getMasterRequest());

        $gateway->initialize($bridge->initializationParameters());

        return $gateway;
    }

    /**
     * @inheritDoc
     */
    public function getBridge($name): BridgeInterface
    {
        if (!isset($this->bridges[$name])) {
            throw new BridgeNotSetException($name);
        }

        return $this->bridges[$name];
    }

    /**
     * @param string          $name
     * @param BridgeInterface $bridge
     */
    public function addBridge(string $name, BridgeInterface $bridge): void
    {
        $this->bridges[$name] = $bridge;
    }
}
