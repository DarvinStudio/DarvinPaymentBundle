<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.07.2018
 * Time: 23:52
 */

namespace Darvin\PaymentBundle\Gateway\Factory;


use Darvin\PaymentBundle\Gateway\ParametersBridge\GatewayParametersBridgeInterface;
use Omnipay\Common\AbstractGateway;

interface GatewayFactoryInterface
{
    /**
     * @param $name
     *
     * @return AbstractGateway
     */
    public function createGateway($name);

    /**
     * @param $name
     *
     * @return GatewayParametersBridgeInterface
     */
    public function getGatewayParametersBridge($name);
}