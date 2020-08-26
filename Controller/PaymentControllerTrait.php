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

use Darvin\PaymentBundle\Bridge\BridgeInterface;
use Darvin\PaymentBundle\Bridge\Exception\BridgeNotExistsException;
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Omnipay\Common\GatewayInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Payment controller trait
 */
trait PaymentControllerTrait
{
    /**
     * @param string $gatewayName Gateway name
     *
     * @return \Omnipay\Common\GatewayInterface
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getGateway(string $gatewayName): GatewayInterface
    {
        try {
            return $this->getGatewayFactory()->createGateway($gatewayName);
        } catch (BridgeNotExistsException $ex) {
            throw new NotFoundHttpException($ex->getMessage());
        }
    }

    /**
     * @param string $gatewayName Gateway name
     *
     * @return \Darvin\PaymentBundle\Bridge\BridgeInterface
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getBridge(string $gatewayName): BridgeInterface
    {
        try {
            return $this->getGatewayFactory()->getBridge($gatewayName);
        } catch (BridgeNotExistsException $ex) {
            throw new NotFoundHttpException($ex->getMessage());
        }
    }

    protected function getPaymentByToken(string $token): Payment
    {
        $payment = $this
            ->getEntityManager()
            ->getRepository(Payment::class)
            ->findOneBy(['actionToken' => $token]);

        if (null === $payment) {
            throw new NotFoundHttpException(sprintf('Unable to find payment with token %s', $token));
        }

        return $payment;
    }

    /**
     * @return GatewayFactoryInterface
     */
    abstract protected function getGatewayFactory(): GatewayFactoryInterface;

    /**
     * @return EntityManagerInterface
     */
    abstract protected function getEntityManager(): EntityManagerInterface;
}
