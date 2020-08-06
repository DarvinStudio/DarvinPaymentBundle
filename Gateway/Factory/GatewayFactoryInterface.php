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
