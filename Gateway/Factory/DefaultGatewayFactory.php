<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.07.2018
 * Time: 23:54
 */

namespace Darvin\PaymentBundle\Gateway\Factory;


use Darvin\PaymentBundle\Gateway\ParametersBridge\GatewayParametersBridgeInterface;
use Darvin\PaymentBundle\Gateway\Factory\Exception\ParametersBridgeNotSetException;
use Omnipay\Omnipay;
use Symfony\Component\HttpFoundation\RequestStack;

class DefaultGatewayFactory implements GatewayFactoryInterface
{
    /**
     * @var array|GatewayParametersBridgeInterface[]
     */
    protected $bridges = [];

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * DefaultGatewayFactory constructor.
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
    public function createGateway($name)
    {
        $bridge = $this->getGatewayParametersBridge($name);
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
    public function getGatewayParametersBridge($name)
    {
        if (!isset($this->bridges[$name])) {
            throw new ParametersBridgeNotSetException($name);
        }

        return $this->bridges[$name];
    }

    /**
     * @param                                  $name
     * @param GatewayParametersBridgeInterface $bridge
     */
    public function addGatewayParametersBridge($name, GatewayParametersBridgeInterface $bridge)
    {
        $this->bridges[$name] = $bridge;
    }
}
