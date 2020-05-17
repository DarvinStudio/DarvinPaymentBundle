<?php declare(strict_types=1);

namespace Darvin\PaymentBundle\Controller;

use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\Form\Type\GatewayRedirectType;
use Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface;
use Darvin\PaymentBundle\PaymentManager\PaymentManagerInterface;
use Darvin\PaymentBundle\Token\Manager\PaymentTokenManagerInterface;
use Darvin\PaymentBundle\UrlBuilder\PaymentUrlBuilderInterface;
use Omnipay\Common\Message\RedirectResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class PaymentController
 * @package Darvin\PaymentBundle\Controller
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
    )
    {
        $this->gatewayFactory = $gatewayFactory;
        $this->paymentManager = $paymentManager;
        $this->paymentUrlBuilder = $paymentUrlBuilder;
        $this->tokenManager = $tokenManager;
    }

    /**
     * @param string $gatewayName
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function purchaseAction($gatewayName, $id)
    {
        $gateway = $this->gatewayFactory->createGateway($gatewayName);
        $bridge = $this->gatewayFactory->getBridge($gatewayName);

        $payment = $this->paymentManager->findById($id);
        if (!$payment) {
            throw $this->createNotFoundException('Payment with id '.$id.' not found');
        }

        if (!$gateway->supportsPurchase()) {
            throw new \Exception(sprintf("%s doesn't support purchase method", $gatewayName));
        }

        $this->paymentManager->markAsPending($payment);
        $response = $gateway->purchase($bridge->purchaseParameters($payment))->send();

        if ($response->getTransactionReference()) {
            $this->paymentManager->setTransactionReference($payment, $response->getTransactionReference());
        }

        if ($response->isRedirect() && $response instanceof RedirectResponseInterface) {
            if ($response->getRedirectMethod() != 'POST') {
                return $this->redirect($response->getRedirectUrl());
            }

            $form = $this->createForm(GatewayRedirectType::class, $response->getRedirectData(), [
                'action' => $response->getRedirectUrl(),
                'method' => $response->getRedirectMethod()
            ]);

            return $this->render('@DarvinPayment/Payment/purchase.html.twig', [
                'form'     => $form->createView(),
                'payment'  => $payment,
                'response' => $response,
                'gateway'  => $gateway
            ]);

        } elseif ($response->isSuccessful()) {
            $this->paymentManager->markAsPaid($payment);

            return $this->redirect($this->paymentUrlBuilder->getSuccessUrl($payment, $gatewayName));
        } elseif ($response->isCancelled()) {
            $this->paymentManager->markAsCanceled($payment);

            return $this->redirect($this->paymentUrlBuilder->getCanceledUrl($payment, $gatewayName));
        }
    }

    /**
     * @param $gatewayName
     * @param $token
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function successPurchaseAction($gatewayName, $token)
    {
        $payment = $this->getPaymentFromToken($token);

        $gateway = $this->gatewayFactory->createGateway($gatewayName);
        $bridge = $this->gatewayFactory->getBridge($gatewayName);

        if (!$gateway->supportsCompletePurchase()) {
            throw new \Exception(sprintf("%s doesn't support complete purchase method", $gatewayName));
        }

        $response = $gateway->completePurchase($bridge->completePurchaseParameters($payment))->send();

        if ($response->isSuccessful()) {
            $this->paymentManager->markAsPaid($payment);

            return $this->render('@DarvinPayment/Payment/success.html.twig', [
                'payment'  => $payment,
                'gateway'  => $gateway,
                'response' => $response
            ]);
        }

        return $this->redirect($response->isCancelled()
            ? $this->paymentUrlBuilder->getCanceledUrl($payment, $gatewayName)
            : $this->paymentUrlBuilder->getFailedUrl($payment, $gatewayName)
        );
    }

    /**
     * @param $gatewayName
     * @param $token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function failedPurchaseAction($gatewayName, $token)
    {
        $payment = $this->getPaymentFromToken($token);

        $gateway = $this->gatewayFactory->createGateway($gatewayName);

        $this->paymentManager->markAsFailed($payment);

        return $this->render('@DarvinPayment/Payment/failed.html.twig', [
            'payment'  => $payment,
            'gateway'  => $gateway
        ]);
    }

    /**
     * @param $gatewayName
     * @param $token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function cancelPurchaseAction($gatewayName, $token)
    {
        $payment = $this->getPaymentFromToken($token);

        $gateway = $this->gatewayFactory->createGateway($gatewayName);

        $this->paymentManager->markAsCanceled($payment);

        return $this->render('@DarvinPayment/Payment/canceled.html.twig', [
            'payment'  => $payment,
            'gateway'  => $gateway
        ]);
    }

    /**
     * @param $token
     *
     * @return PaymentInterface
     */
    protected function getPaymentFromToken($token)
    {
        $payment = $this->tokenManager->findPayment($token);
        if (!$payment) {
            throw $this->createNotFoundException(sprintf(
                "Unable to find payment with token %s",
                $token
            ));
        }

        return $payment;
    }
}
