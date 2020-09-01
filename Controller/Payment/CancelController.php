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
 * Controller for the canceling payment
 */
class CancelController extends AbstractController
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

        $this->validatePayment($payment, Transitions::CANCEL);
        $this->workflow->apply($payment, Transitions::CANCEL);
        $this->entityManager->flush();

        return new Response(
            $this->twig->render('@DarvinPayment/payment/cancel.html.twig', [
                'payment' => $payment,
                'gateway' => $gateway,
            ])
        );
    }
}
