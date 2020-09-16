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
use Darvin\PaymentBundle\DBAL\Type\PaymentStateType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Success payment controller
 */
class SuccessController extends AbstractController
{
    /**
     * @param string $token Payment token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function __invoke(string $token): Response
    {
        $payment = $this->getPaymentByToken($token);

        if (!in_array($payment->getState(),[
            PaymentStateType::COMPLETED,
            PaymentStateType::AUTHORIZED
        ], true)) {
            $this->logger->error(
                $this->translator->trans('payment.log.error.success', [
                    '%method%' => __METHOD__,
                    '%state%'  => $payment->getState(),
                ]),
                ['payment' => $payment]
            );

            throw new NotFoundHttpException();
        }

        return new Response(
            $this->twig->render('@DarvinPayment/payment/success.html.twig', [
                'payment' => $payment,
            ])
        );
    }
}
