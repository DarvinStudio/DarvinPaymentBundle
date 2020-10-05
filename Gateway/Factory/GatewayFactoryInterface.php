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
use Omnipay\Common\GatewayInterface;

/**
 * Interface of gateway factory
 */
interface GatewayFactoryInterface
{
    /**
     * @param string $name Bridge name
     *
     * @return \Omnipay\Common\GatewayInterface
     * @throws \Darvin\PaymentBundle\Bridge\Exception\BridgeNotExistsException
     */
    public function createGateway(string $name): GatewayInterface;

    /**
     * @param string $name Bridge name
     *
     * @return \Darvin\PaymentBundle\Bridge\BridgeInterface
     * @throws \Darvin\PaymentBundle\Bridge\Exception\BridgeNotExistsException
     */
    public function getBridge(string $name): BridgeInterface;
}
