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
use Darvin\PaymentBundle\Workflow\Transitions;
use Symfony\Component\HttpFoundation\Response;

/**
 * Purchase controller
 */
class PurchaseController extends AbstractController
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
        $payment = $this->getPaymentByToken($token);
        $gateway = $this->getGateway($gatewayName);
        $bridge = $this->getBridge($gatewayName);

        $this->validatePayment($payment, Transitions::PURCHASE, $gateway);
        $this->validateGateway($gateway, 'purchase');

        if ($payment->hasRedirect()) {
            return $this->createPaymentResponse($payment);
        }

        try {
            $response = $gateway->purchase($bridge->purchaseParameters($payment))->send();
        } catch (\Exception $ex) {
            $this->logger->critical(sprintf('%s: %s', __METHOD__, $ex->getMessage()), ['payment' => $payment]);

            return $this->createErrorResponse($payment);
        }

        $payment
            ->setTransactionReference($response->getTransactionReference())
            ->setGatewayName($gatewayName);

        $this->em->flush();

        if ($response->isSuccessful() && $response->isRedirect()) {
            $payment->setRedirect($this->redirectFactory->createRedirect($response, $bridge->getSessionTimeout()));
            $this->em->flush();

            $this->logger->info($this->translator->trans('payment.log.info.created_redirect'), ['payment' => $payment]);

            return $this->createPaymentResponse($payment);
        }

        $this->logger->error(
            $this->translator->trans('payment.log.error.bad_response', [
                '%method%'  => __METHOD__,
                '%code%'    => $response->getCode(),
                '%message%' => $response->getMessage(),
            ]),
            ['payment' => $payment]
        );

        return $this->createErrorResponse($payment);
    }
}
