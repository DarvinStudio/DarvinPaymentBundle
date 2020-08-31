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
use Darvin\PaymentBundle\DBAL\Type\PaymentStateType;
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Workflow\Transitions;
use Omnipay\Common\GatewayInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Authorize success controller
 */
class AuthorizeSuccessController extends AbstractController implements PreCheckControllerInterface
{
    /**
     * @param string $gatewayName Gateway name
     * @param string $token       Payment token
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function __invoke(string $gatewayName, string $token): Response
    {
        $bridge = $this->getBridge($gatewayName);
        $gateway = $this->getGateway($gatewayName);
        $payment = $this->getPaymentByToken($token);

        if ($payment->getState() === PaymentStateType::AUTHORIZED) {
            return new Response(
                $this->getTwig()->render('@DarvinPayment/payment/authorize_success.html.twig', [
                    'payment' => $payment,
                ])
            );
        }

        $this->preCheckPayment($gateway, $payment);

        $response = $gateway->completeAuthorize($bridge->completeAuthorizeParameters($payment))->send();

        if ($response->isSuccessful()) {
            $this->getWorkflow()->apply($payment, Transitions::AUTHORIZE);
            $this->getEntityManager()->flush();

            return new Response(
                $this->getTwig()->render('@DarvinPayment/payment/authorize_success.html.twig', [
                    'payment' => $payment,
                ])
            );
        }

        return new RedirectResponse($this->getPaymentUrlBuilder()->getFailedUrl($payment, $gatewayName));
    }

    /**
     * @inheritDoc
     */
    public function preCheckPayment(GatewayInterface $gateway, Payment $payment): void
    {

        if (!$gateway->supportsCompleteAuthorize()) {
            $errorMessage = sprintf('Payment gateway %s doesn\'t support completeAuthorize method', $gateway->getName());

            if (null !== $this->getLogger()) {
                $this->getLogger()->error($errorMessage);
            }

            throw new NotFoundHttpException($errorMessage);
        }

        if (!$this->getWorkflow()->can($payment, Transitions::AUTHORIZE)) {
            $errorMessage = 'This operation is not available for your payment';

            if (null !== $this->getLogger()) {
                $this->getLogger()->error($errorMessage);
            }

            throw new \LogicException($errorMessage);
        }
    }
}
