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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Complete controller
 */
class CompleteController extends AbstractController
{
    /**
     * @param string $token Payment token
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function __invoke(string $token): Response
    {
        $payment = $this->getPaymentByToken($token);
        $gateway = $this->getGateway($payment->getGateway());
        $bridge = $this->getBridge($payment->getGateway());

        if ($this->preAuthorize) {
            $method = 'completeAuthorize';
            $transition = Transitions::AUTHORIZE;
            $parameters = $bridge->completeAuthorizeParameters($payment);
        } else {
            $method = 'completePurchase';
            $transition = Transitions::PURCHASE;
            $parameters = $bridge->completePurchaseParameters($payment);
        }

        $this->validateGateway($gateway, $method);
        $this->validatePayment($payment, $transition);

        try {
            $response = $gateway->{$method}($parameters)->send();

        } catch (\Exception $ex) {
            $this->logger->critical(sprintf('%s: %s', __METHOD__, $ex->getMessage()), ['payment' => $payment]);

            $this->em->flush();

            return $this->createErrorResponse($payment);
        }

        if ($response->isSuccessful()) {
            $this->workflow->apply($payment, $transition);
            $this->em->flush();

            return new RedirectResponse($this->urlBuilder->getSuccessUrl($payment));
        }

        $this->logger->error(
            $this->translator->trans('payment.log.error.bad_response', [
                '%method%'  => __METHOD__,
                '%code%'    => $response->getCode(),
                '%message%' => $response->getMessage(),
            ]),
            ['payment' => $payment]
        );

        $this->em->flush();

        return $this->createErrorResponse($payment);
    }
}
