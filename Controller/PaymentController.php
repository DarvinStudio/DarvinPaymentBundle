<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Controller;

use Darvin\PaymentBundle\Bridge\BridgeInterface;
use Darvin\PaymentBundle\Bridge\Exception\BridgeNotSetException;
use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\Form\Type\GatewayRedirectType;
use Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface;
use Darvin\PaymentBundle\Manager\PaymentManagerInterface;
use Darvin\PaymentBundle\Token\Manager\PaymentTokenManagerInterface;
use Darvin\PaymentBundle\UrlBuilder\Exception\ActionNotImplementedException;
use Darvin\PaymentBundle\UrlBuilder\PaymentUrlBuilderInterface;
use Omnipay\Common\GatewayInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Payment controller
 */
class PaymentController extends AbstractController
{
    /**
     * @var GatewayFactoryInterface
     */
    private $gatewayFactory;

    /**
     * @var PaymentManagerInterface
     */
    private $paymentManager;

    /**
     * @var PaymentUrlBuilderInterface
     */
    private $paymentUrlBuilder;

    /**
     * @var PaymentTokenManagerInterface
     */
    private $tokenManager;

    public function __construct(
        GatewayFactoryInterface $gatewayFactory,
        PaymentManagerInterface $paymentManager,
        PaymentUrlBuilderInterface $paymentUrlBuilder,
        PaymentTokenManagerInterface $tokenManager
    ) {
        $this->gatewayFactory = $gatewayFactory;
        $this->paymentManager = $paymentManager;
        $this->paymentUrlBuilder = $paymentUrlBuilder;
        $this->tokenManager = $tokenManager;
    }

    /**
     * @param string $gatewayName Gateway name
     * @param int    $id          Payment ID
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function purchaseAction(string $gatewayName, int $id): Response
    {
        $bridge = $this->getBridge($gatewayName);
        $gateway = $this->getGateway($gatewayName);
        $payment = $this->getPaymentFromId($id);

        $this->paymentManager->markAsPending($payment);
        $response = $gateway->purchase($bridge->purchaseParameters($payment))->send();

        if ($response->getTransactionReference() !== null) {
            $this->paymentManager->setTransactionReference($payment, $response->getTransactionReference());
        }

        if ($response->isRedirect()) {
            if ($response->getRedirectMethod() !== 'POST') {
                return $this->redirect($response->getRedirectUrl());
            }

            $form = $this->createForm(GatewayRedirectType::class, $response->getRedirectData(), [
                'action' => $response->getRedirectUrl(),
                'method' => $response->getRedirectMethod(),
            ]);

            return $this->render('@DarvinPayment/Payment/purchase.html.twig', [
                'form'     => $form->createView(),
                'payment'  => $payment,
                'response' => $response,
                'gateway'  => $gateway,
            ]);
        }

        if ($response->isSuccessful()) {
            $this->paymentManager->markAsPaid($payment);

            return $this->createRedirect(function(PaymentInterface $payment, string $gatewayName): RedirectResponse {
                return $this->redirect($this->paymentUrlBuilder->getSuccessUrl($payment, $gatewayName));
            });
        }

        if ($response->isCancelled()) {
            $this->paymentManager->markAsCanceled($payment);

            return $this->createRedirect(function(PaymentInterface $payment, string $gatewayName): RedirectResponse {
                return $this->redirect($this->paymentUrlBuilder->getCanceledUrl($payment, $gatewayName));
            });
        }

        throw $this->createNotFoundException('Undefined response');
    }

    /**
     * @param string $gatewayName Gateway name
     * @param string $token       Payment token
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function successPurchaseAction(string $gatewayName, string $token): Response
    {
        $bridge = $this->getBridge($gatewayName);
        $gateway = $this->getGateway($gatewayName);
        $payment = $this->getPaymentByToken($token);

        $response = $gateway->completePurchase($bridge->completePurchaseParameters($payment))->send();

        if ($response->isSuccessful()) {
            $this->paymentManager->markAsPaid($payment);

            return $this->render('@DarvinPayment/Payment/success.html.twig', [
                'payment'  => $payment,
                'gateway'  => $gateway,
                'response' => $response
            ]);
        }

        $this->paymentManager->markAsFailed($payment);

        return $this->createRedirect(function($payment, $gatewayName): RedirectResponse {
            return $this->redirect($this->paymentUrlBuilder->getFailedUrl($payment, $gatewayName));
        });
    }

    /**
     * @param callable $callable
     *
     * @return RedirectResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function createRedirect(callable $callable): RedirectResponse
    {
        try {
            return $callable();
        } catch (ActionNotImplementedException $ex) {
            throw $this->createNotFoundException($ex->getMessage());
        }
    }

    /**
     * @param string $gatewayName Gateway name
     * @param string $token       Payment token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function failedPurchaseAction(string $gatewayName, string $token): Response
    {
        $payment = $this->getPaymentByToken($token);

        $gateway = $this->getGateway($gatewayName);

        $this->paymentManager->markAsFailed($payment);

        return $this->render('@DarvinPayment/Payment/failed.html.twig', [
            'payment'  => $payment,
            'gateway' => $gateway,
        ]);
    }

    /**
     * @param string $gatewayName Gateway name
     * @param string $token       Payment token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cancelPurchaseAction(string $gatewayName, string $token): Response
    {
        $payment = $this->getPaymentByToken($token);

        $gateway = $this->getGateway($gatewayName);

        $this->paymentManager->markAsCanceled($payment);

        return $this->render('@DarvinPayment/Payment/canceled.html.twig', [
            'payment' => $payment,
            'gateway' => $gateway,
        ]);
    }

    /**
     * @param int $id Payment ID
     *
     * @return PaymentInterface
     */
    private function getPaymentFromId(int $id): PaymentInterface
    {
        $payment = $this->paymentManager->findById($id);
        if (!$payment) {
            throw $this->createNotFoundException(sprintf('Payment with id %s not found', $id));
        }

        return $payment;
    }

    /**
     * @param string $token Payment token
     *
     * @return PaymentInterface
     */
    private function getPaymentByToken(string $token): PaymentInterface
    {
        $payment = $this->tokenManager->findPayment($token);
        if (!$payment) {
            throw $this->createNotFoundException(sprintf('Unable to find payment with token %s', $token));
        }

        return $payment;
    }

    /**
     * @param string $gatewayName Gateway name
     *
     * @return GatewayInterface
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function getGateway(string $gatewayName): GatewayInterface
    {
        try {
            $gateway = $this->gatewayFactory->createGateway($gatewayName);
        } catch (BridgeNotSetException $ex) {
            throw $this->createNotFoundException($ex->getMessage());
        }

        if (!$gateway->supportsPurchase()) {
            throw $this->createNotFoundException(sprintf("%s doesn't support purchase method", $gatewayName));
        }

        return $gateway;
    }

    /**
     * @param string $gatewayName Gateway name
     *
     * @return BridgeInterface
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function getBridge(string $gatewayName): BridgeInterface
    {
        try {
            $bridge = $this->gatewayFactory->getBridge($gatewayName);
        } catch (BridgeNotSetException $ex) {
            throw $this->createNotFoundException($ex->getMessage());
        }

        return $bridge;
    }
}
