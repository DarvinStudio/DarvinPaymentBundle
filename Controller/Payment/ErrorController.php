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
use Symfony\Component\HttpFoundation\Response;

/**
 * Error payment controller
 */
class ErrorController extends AbstractController
{
    /**
     * @param string $token Payment token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(string $token): Response
    {
        $payment = $this->getPaymentByToken($token);

        return new Response($this->twig->render('@DarvinPayment/payment/error.html.twig', [
            'payment' => $payment,
        ]));
    }
}
