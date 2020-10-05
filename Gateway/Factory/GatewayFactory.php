<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Gateway\Factory;

use Darvin\PaymentBundle\Bridge\BridgeInterface;
use Darvin\PaymentBundle\Bridge\Exception\BridgeNotExistsException;
use Omnipay\Common\GatewayInterface;
use Omnipay\Omnipay;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Gateway factory
 */
class GatewayFactory implements GatewayFactoryInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Darvin\PaymentBundle\Bridge\BridgeInterface[]
     */
    private $bridges;

    /**
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack Request stack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;

        $this->bridges = [];
    }

    /**
     * @param string                                       $name   Bridge name
     * @param \Darvin\PaymentBundle\Bridge\BridgeInterface $bridge Bridge
     */
    public function addBridge(string $name, BridgeInterface $bridge): void
    {
        $this->bridges[$name] = $bridge;
    }

    /**
     * {@inheritDoc}
     */
    public function createGateway(string $name): GatewayInterface
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
     * {@inheritDoc}
     */
    public function getBridge(string $name): BridgeInterface
    {
        if (!isset($this->bridges[$name])) {
            throw new BridgeNotExistsException($name);
        }

        return $this->bridges[$name];
    }
}
