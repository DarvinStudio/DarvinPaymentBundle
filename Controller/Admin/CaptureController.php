<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Controller\Admin;

use Darvin\PaymentBundle\Controller\AbstractController;
use Darvin\PaymentBundle\Workflow\Transitions;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authorize success controller
 */
class CaptureController extends AbstractController
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

        $this->validateGateway($gateway, 'capture');
        $this->validatePayment($payment, Transitions::CAPTURE);

        $response = $gateway->capture($bridge->captureParameters($payment))->send();

        if ($response->isSuccessful()) {
            $this->workflow->apply($payment, Transitions::CAPTURE);
            $this->entityManager->flush();

            return new Response(
                $this->twig->render('@DarvinPayment/admin/capture.html.twig', [
                    'payment' => $payment,
                ])
            );
        }

        return new RedirectResponse($this->urlBuilder->getFailUrl($payment, $gatewayName));
    }
}
