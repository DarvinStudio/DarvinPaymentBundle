<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Controller;

use Darvin\PaymentBundle\Entity\Payment;
use Omnipay\Common\GatewayInterface;

/**
 * Purchase controller
 */
interface PreCheckControllerInterface
{
    /**
     * @param \Omnipay\Common\GatewayInterface     $gateway
     * @param \Darvin\PaymentBundle\Entity\Payment $payment
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \LogicException
     */
     public function preCheckPayment(GatewayInterface $gateway, Payment $payment): void;
}
