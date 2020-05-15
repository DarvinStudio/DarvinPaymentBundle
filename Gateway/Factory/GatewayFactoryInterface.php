<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.07.2018
 * Time: 23:52
 */

namespace Darvin\PaymentBundle\Gateway\Factory;


use Darvin\PaymentBundle\Gateway\Factory\Exception\ParametersBridgeNotSetException;
use Darvin\PaymentBundle\Gateway\ParametersBridge\GatewayParametersBridgeInterface;
use Omnipay\Common\GatewayInterface;

interface GatewayFactoryInterface
{
    /**
     * @param $name
     *
     * @return GatewayInterface
     */
    public function createGateway($name): GatewayInterface;

    /**
     * @param $name
     *
     * @return GatewayParametersBridgeInterface
     *
     * @throws ParametersBridgeNotSetException
     */
    public function getGatewayParametersBridge($name): GatewayParametersBridgeInterface;
}
