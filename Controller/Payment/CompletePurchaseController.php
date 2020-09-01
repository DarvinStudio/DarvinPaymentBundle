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
 * Purchase success controller
 */
class CompletePurchaseController extends AbstractController
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

        $this->validatePayment($payment, Transitions::PURCHASE);
        $this->validateGateway($gateway, 'completePurchase');

        try {
            $response = $gateway->completePurchase($bridge->completePurchaseParameters($payment))->send();
        } catch (\Exception $ex) {
            $this->addErrorLog(sprintf('%s: %s', __METHOD__, $ex->getMessage()));

            return new RedirectResponse($this->urlBuilder->getFailUrl($payment, $gatewayName));
        }

        if ($response->isSuccessful()) {
            $this->workflow->apply($payment, Transitions::PURCHASE);
            $this->entityManager->flush();

            return new RedirectResponse($this->urlBuilder->getSuccessUrl($payment, $gatewayName));
        }

        $errorMessage = sprintf(
            '%s: Can\'t handler response for payment id %s and gateway %s. Response code: %s. Response message: %s',
            __METHOD__,
            $payment->getId(),
            $gatewayName,
            $response->getCode(),
            $response->getMessage()
        );

        $this->addErrorLog($errorMessage);
        return new RedirectResponse($this->urlBuilder->getFailUrl($payment, $gatewayName));
    }
}
