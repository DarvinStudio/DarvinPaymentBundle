<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Bridge\Exception;

/**
 * Exception for case bridge not set
 */
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
