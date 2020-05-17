<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 07.07.2018
 * Time: 0:01
 */

namespace Darvin\PaymentBundle\Bridge\Exception;


class BridgeNotSetException extends \Exception
{
    /**
     * @var string
     */
    protected $gatewayName;

    /**
     * BridgeNotExistException constructor.
     *
     * @param string $gatewayName
     */
    public function __construct(string $gatewayName)
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
    public function getGatewayName(): string
    {
        return $this->gatewayName;
    }
}
