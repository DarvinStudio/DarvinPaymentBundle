<?php

namespace Darvin\PaymentBundle\Controller;

use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\Form\Type\GatewayRedirectType;
use Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface;
use Darvin\PaymentBundle\PaymentManager\PaymentManagerInterface;
use Darvin\PaymentBundle\Token\Manager\TokenManagerInterface;
use Darvin\PaymentBundle\UrlBuilder\PaymentUrlBuilderInterface;
use Omnipay\Common\Message\RedirectResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class PaymentController
 * @package Darvin\PaymentBundle\Controller
 */
class PaymentController extends Controller
{
    /**
     * @param                  $gatewayName
     * @param PaymentInterface $payment
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function purchaseAction($gatewayName, PaymentInterface $payment)
    {
        $gateway = $this->getGatewayFactory()->createGateway($gatewayName);
        $bridge = $this->getGatewayFactory()->getGatewayParametersBridge($gatewayName);
        $manager = $this->getPaymentManager();
        $urlBuilder = $this->getPaymentUrlBuilder();

        if (!$gateway->supportsPurchase()) {
            throw new \Exception(sprintf("%s doesn't support purchase method", $gatewayName));
        }

        $response = $gateway->purchase($bridge->purchaseParameters($payment))->send();

        if ($response->isRedirect() && $response instanceof RedirectResponseInterface) {
            $manager->markAsPending($payment);

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
            $manager->markAsPaid($payment);

            return $this->redirect($urlBuilder->getSuccessUrl($payment, $gatewayName));
        } elseif ($response->isCancelled()) {
            $manager->markAsCanceled($payment);

            return $this->redirect($urlBuilder->getCanceledUrl($payment, $gatewayName));
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

        $gateway = $this->getGatewayFactory()->createGateway($gatewayName);
        $bridge = $this->getGatewayFactory()->getGatewayParametersBridge($gatewayName);
        $manager = $this->getPaymentManager();
        $urlBuilder = $this->getPaymentUrlBuilder();

        if (!$gateway->supportsCompletePurchase()) {
            throw new \Exception(sprintf("%s doesn't support complete purchase method", $gatewayName));
        }

        $response = $gateway->completePurchase($bridge->purchaseParameters($payment))->send();

        if ($response->isSuccessful()) {
            $manager->markAsPaid($payment);

            return $this->render('@DarvinPayment/Payment/success.html.twig', [
                'payment'  => $payment,
                'gateway'  => $gateway,
                'response' => $response
            ]);
        }

        return $this->redirect($response->isCancelled() ?
            $urlBuilder->getCanceledUrl($payment, $gateway) :
            $urlBuilder->getFailedUrl($payment, $gateway)
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

        $gateway = $this->getGatewayFactory()->createGateway($gatewayName);
        $manager = $this->getPaymentManager();

        $manager->markAsFailed($payment);

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

        $gateway = $this->getGatewayFactory()->createGateway($gatewayName);
        $manager = $this->getPaymentManager();

        $manager->markAsCanceled($payment);

        return $this->render('@DarvinPayment/Payment/canceled.html.twig', [
            'payment'  => $payment,
            'gateway'  => $gateway
        ]);
    }

    /**
     * @return \Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface
     */
    protected function getGatewayFactory()
    {
        return $this->get(GatewayFactoryInterface::class);
    }

    /**
     * @return PaymentManagerInterface
     */
    protected function getPaymentManager()
    {
        return $this->get(PaymentManagerInterface::class);
    }

    /**
     * @return \Darvin\PaymentBundle\UrlBuilder\PaymentUrlBuilderInterface
     */
    protected function getPaymentUrlBuilder()
    {
        return $this->get(PaymentUrlBuilderInterface::class);
    }

    /**
     * @return \Darvin\PaymentBundle\Token\Manager\TokenManagerInterface
     */
    protected function getTokenManager()
    {
        return $this->get(TokenManagerInterface::class);
    }

    /**
     * @param $token
     *
     * @return PaymentInterface
     */
    protected function getPaymentFromToken($token)
    {
        $payment = $this->getTokenManager()->findPayment($token);
        if (!$payment) {
            $this->createNotFoundException(sprintf(
                "Unable to find payment with token %s",
                $token
            ));
        }

        return $payment;
    }
}
