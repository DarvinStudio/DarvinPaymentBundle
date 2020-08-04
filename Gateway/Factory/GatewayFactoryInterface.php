<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.07.2018
 * Time: 23:52
 */

namespace Darvin\PaymentBundle\Gateway\Factory;


use Darvin\PaymentBundle\Bridge\BridgeInterface;
use Darvin\PaymentBundle\Bridge\Exception\BridgeNotSetException;
use Omnipay\Common\GatewayInterface;

interface GatewayFactoryInterface
{
    /**
     * @param $name
     *
     * @return GatewayInterface
     *
     * @throws BridgeNotSetException
     */
    public function createGateway($name): GatewayInterface;

    /**
     * @param $name
     *
     * @return BridgeInterface
     *
     * @throws BridgeNotSetException
     */
    public function getBridge($name): BridgeInterface;
}
