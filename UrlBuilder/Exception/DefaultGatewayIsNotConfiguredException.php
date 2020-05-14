<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 08.07.2018
 * Time: 17:39
 */

namespace Darvin\PaymentBundle\UrlBuilder\Exception;


use Throwable;

class DefaultGatewayIsNotConfiguredException extends \Exception
{
    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct("Default payment gateway is not configured");
    }

}
