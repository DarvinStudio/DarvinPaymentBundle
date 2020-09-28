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
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Form\Type\GatewayRedirectType;
use Darvin\PaymentBundle\Redirect\RedirectFactoryInterface;
use Darvin\PaymentBundle\Payment\Operations;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Purchase payment controller
 */
class PurchaseController extends AbstractController
{
    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \Darvin\PaymentBundle\Redirect\RedirectFactoryInterface
     */
    private $redirectFactory;

    /**
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory Form factory
     */
    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param \Darvin\PaymentBundle\Redirect\RedirectFactoryInterface $redirectFactory Redirect factory
     */
    public function setRedirectFactory(RedirectFactoryInterface $redirectFactory): void
    {
        $this->redirectFactory = $redirectFactory;
    }

    /**
     * @param string $gatewayName Gateway name
     * @param string $token       Payment token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(string $gatewayName, string $token): Response
    {
        $payment = $this->getPaymentByToken($token);
        $gateway = $this->getGateway($gatewayName);
        $bridge  = $this->getBridge($gatewayName);

        if ($this->preAuthorize) {
            $method     = 'authorize';
            $operation  = Operations::AUTHORIZE;
            $parameters = $bridge->authorizeParameters($payment);
        } else {
            $method     = 'purchase';
            $operation  = Operations::PURCHASE;
            $parameters = $bridge->purchaseParameters($payment);
        }

        $this->validateGateway($gateway, $method);
        $this->validatePayment($payment, $operation, $gatewayName);

        if ($payment->hasRedirect()) {
            return $this->createPaymentResponse($payment);
        }
        try {
            $response = $gateway->{$method}($parameters)->send();
        } catch (\Exception $ex) {
            $this->logger->critical(sprintf('%s: %s', __METHOD__, $ex->getMessage()), ['payment' => $payment]);

            return $this->createErrorResponse($payment);
        }

        $payment
            ->setTransactionReference($response->getTransactionReference())
            ->setGateway($gatewayName);

        $this->em->flush();

        if ($response->isSuccessful() && $response->isRedirect()) {
            $redirect = $this->redirectFactory->createRedirect($response, $bridge->getSessionTimeout());
            $redirect->setPayment($payment);

            $payment->setRedirect($redirect);

            $this->em->persist($redirect);
            $this->em->flush();

            $this->logger->info(
                $this->translator->trans('info.created_redirect', [], 'payment_event'),
                [
                    'payment' => $payment,
                ]
            );

            return $this->createPaymentResponse($payment);
        }

        $this->logger->error(
            $this->translator->trans('error.bad_response', [
                '%method%'  => __METHOD__,
                '%code%'    => $response->getCode(),
                '%message%' => $response->getMessage(),
            ], 'payment_event'),
            [
                'payment' => $payment,
            ]
        );

        return $this->createErrorResponse($payment);
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment Payment
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    private function createPaymentResponse(Payment $payment): Response
    {
        $redirect = $payment->getRedirect();

        if (null === $redirect) {
            throw new \LogicException('Redirect could not be empty');
        }
        if ($redirect->isExpired()) {
            $this->workflow->apply($payment, Operations::EXPIRE);

            $this->em->flush();

            $this->logger->warning(
                $this->translator->trans('warning.session_expired', [], 'payment_event'),
                [
                    'payment' => $payment,
                ]
            );

            return $this->createErrorResponse($payment);
        }
        if ($redirect->getMethod() !== 'POST') {
            return new RedirectResponse($redirect->getUrl());
        }

        $form = $this->formFactory->create(GatewayRedirectType::class, $redirect->getData(), [
            'action' => $redirect->getUrl(),
            'method' => $redirect->getMethod(),
        ]);

        return new Response($this->twig->render('@DarvinPayment/payment/purchase.html.twig', [
            'form' => $form->createView(),
        ]));
    }
}
