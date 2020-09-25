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
use Darvin\PaymentBundle\Workflow\Transitions;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fail payment controller
 */
class FailController extends AbstractController
{
    /**
     * @param string $token Payment token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(string $token): Response
    {
        $payment = $this->getPaymentByToken($token);

        if (PaymentStateType::FAILED === $payment->getState()) {
            return new Response($this->twig->render('@DarvinPayment/payment/fail.html.twig', [
                'payment' => $payment,
            ]));
        }

        $this->validatePayment($payment, Transitions::FAIL);

        $this->workflow->apply($payment, Transitions::FAIL);

        $this->em->flush();

        return new Response($this->twig->render('@DarvinPayment/payment/fail.html.twig', [
            'payment' => $payment,
        ]));
    }
}
