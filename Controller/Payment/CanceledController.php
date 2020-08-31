<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Controller\Payment;

use Darvin\PaymentBundle\Controller\AbstractController;
use Darvin\PaymentBundle\Controller\PreCheckControllerInterface;
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Workflow\Transitions;
use Omnipay\Common\GatewayInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for the canceling payment
 */
class CanceledController extends AbstractController implements PreCheckControllerInterface
{
    /**
     * @param string $gatewayName Gateway name
     * @param string $token       Payment token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function __invoke(string $gatewayName, string $token): Response
    {
        $payment = $this->getPaymentByToken($token);
        $gateway = $this->getGateway($gatewayName);

        $this->preCheckPayment($gateway, $payment);

        $this->getWorkflow()->apply($payment, Transitions::CANCEL);
        $this->getEntityManager()->flush();

        return new Response(
            $this->getTwig()->render('@DarvinPayment/payment/canceled.html.twig', [
                'payment' => $payment,
                'gateway' => $gateway,
            ])
        );
    }

    /**
     * @inheritDoc
     */
    public function preCheckPayment(GatewayInterface $gateway, Payment $payment): void
    {
        if (!$this->getWorkflow()->can($payment, Transitions::CANCEL)) {
            $errorMessage = 'This operation is not available for your payment';

            if (null !== $this->getLogger()) {
                $this->getLogger()->error($errorMessage);
            }

            throw new \LogicException($errorMessage);
        }
    }
}
