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
use Darvin\PaymentBundle\Bridge\Exception\BridgeNotExistsException;
use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\Form\Type\GatewayRedirectType;
use Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface;
use Darvin\PaymentBundle\Payment\Manager\PaymentManagerInterface;
use Darvin\PaymentBundle\State\Manager\StateManagerInterface;
use Darvin\PaymentBundle\Token\Manager\PaymentTokenManagerInterface;
use Darvin\PaymentBundle\UrlBuilder\PaymentUrlBuilderInterface;
use Omnipay\Common\GatewayInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Payment controller
 */
class PaymentController extends AbstractController
{
    /**
     * @var \Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface
     */
    private $gatewayFactory;

    /**
     * @var \Darvin\PaymentBundle\Payment\Manager\PaymentManagerInterface
     */
    private $paymentManager;

    /**
     * @var \Darvin\PaymentBundle\State\Manager\StateManagerInterface
     */
    private $stateManager;

    /**
     * @var \Darvin\PaymentBundle\UrlBuilder\PaymentUrlBuilderInterface
     */
    private $paymentUrlBuilder;

    /**
     * @var \Darvin\PaymentBundle\Token\Manager\PaymentTokenManagerInterface
     */
    private $tokenManager;

    public function __construct(
        GatewayFactoryInterface $gatewayFactory,
        PaymentManagerInterface $paymentManager,
        StateManagerInterface $stateManager,
        PaymentUrlBuilderInterface $paymentUrlBuilder,
        PaymentTokenManagerInterface $tokenManager
    ) {
        $this->gatewayFactory = $gatewayFactory;
        $this->paymentManager = $paymentManager;
        $this->stateManager = $stateManager;
        $this->paymentUrlBuilder = $paymentUrlBuilder;
        $this->tokenManager = $tokenManager;
    }

    /**
     * @param string                                        $gatewayName Gateway name
     * @param \Darvin\PaymentBundle\Entity\PaymentInterface $payment     Payment
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function purchaseAction(string $gatewayName, PaymentInterface $payment): Response
    {
        $bridge = $this->getBridge($gatewayName);
        $gateway = $this->getGateway($gatewayName);

        if (!$gateway->supportsPurchase()) {
            throw $this->createNotFoundException(sprintf("%s doesn't support purchase method", $gatewayName));
        }

        $this->stateManager->markAsPending($payment);
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
            $this->stateManager->markAsCompleted($payment);

            return $this->redirect($this->paymentUrlBuilder->getSuccessUrl($payment, $gatewayName));
        }

        if ($response->isCancelled()) {
            $this->stateManager->markAsCanceled($payment);

            return $this->redirect($this->paymentUrlBuilder->getCanceledUrl($payment, $gatewayName));
        }

        throw new \LogicException('Undefined response');
    }

    /**
     * @param string $gatewayName Gateway name
     * @param string $token       Payment token
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function successAction(string $gatewayName, string $token): Response
    {
        $bridge = $this->getBridge($gatewayName);
        $gateway = $this->getGateway($gatewayName);
        $payment = $this->getPaymentByToken($token);

        if ($this->stateManager->isCompleted($payment)) {
            return $this->render('@DarvinPayment/Payment/success.html.twig', [
                'payment'  => $payment,
            ]);
        }

        if (!$gateway->supportsCompletePurchase()) {
            throw $this->createNotFoundException(sprintf('Gateway "%s" doesn\'t support "completePurchase" method', $gatewayName));
        }

        $response = $gateway->completePurchase($bridge->completePurchaseParameters($payment))->send();

        if ($response->isSuccessful()) {
            $this->stateManager->markAsCompleted($payment);

            return $this->render('@DarvinPayment/Payment/success.html.twig', [
                'payment'  => $payment,
            ]);
        }

        $this->stateManager->markAsFailed($payment);

        return $this->redirect($this->paymentUrlBuilder->getFailedUrl($payment, $gatewayName));
    }

    /**
     * @param string $gatewayName Gateway name
     * @param string $token       Payment token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function failedAction(string $gatewayName, string $token): Response
    {
        $payment = $this->getPaymentByToken($token);

        $gateway = $this->getGateway($gatewayName);

        $this->stateManager->markAsFailed($payment);

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
    public function canceledAction(string $gatewayName, string $token): Response
    {
        $payment = $this->getPaymentByToken($token);

        $gateway = $this->getGateway($gatewayName);

        $this->stateManager->markAsCanceled($payment);

        return $this->render('@DarvinPayment/Payment/canceled.html.twig', [
            'payment' => $payment,
            'gateway' => $gateway,
        ]);
    }

    /**
     * @param string $token Payment token
     *
     * @return \Darvin\PaymentBundle\Entity\PaymentInterface
     */
    private function getPaymentByToken(string $token): PaymentInterface
    {
        $payment = $this->tokenManager->findPayment($token);
        if (null === $payment) {
            throw $this->createNotFoundException(sprintf('Unable to find payment with token %s', $token));
        }

        return $payment;
    }

    /**
     * @param string $gatewayName Gateway name
     *
     * @return \Omnipay\Common\GatewayInterface
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function getGateway(string $gatewayName): GatewayInterface
    {
        try {
            return $this->gatewayFactory->createGateway($gatewayName);
        } catch (BridgeNotExistsException $ex) {
            throw $this->createNotFoundException($ex->getMessage());
        }
    }

    /**
     * @param string $gatewayName Gateway name
     *
     * @return \Darvin\PaymentBundle\Bridge\BridgeInterface
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    private function getBridge(string $gatewayName): BridgeInterface
    {
        try {
            return $this->gatewayFactory->getBridge($gatewayName);
        } catch (BridgeNotExistsException $ex) {
            throw $this->createNotFoundException($ex->getMessage());
        }
    }
}
