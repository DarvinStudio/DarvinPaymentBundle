<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 07.07.2018
 * Time: 0:01
 */

namespace Darvin\PaymentBundle\Gateway\Factory\Exception;


class ParametersBridgeNotSetException extends \Exception
{
    protected $gatewayName;

    /**
     * BridgeNotExistException constructor.
     *
     * @param $gatewayName
     */
    public function __construct($gatewayName)
    {
        parent::__construct(sprintf(
            "Parameters bridge for %s gateway not found",
            $gatewayName
        ));
        $this->gatewayName = $gatewayName;
    }

    /**
     * @return string
     */
    public function getGatewayName()
    {
        return $this->gatewayName;
    }
}