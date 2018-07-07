<?php

namespace Darvin\PaymentBundle\Controller;

use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface;
use Darvin\PaymentBundle\PaymentManager\PaymentManagerInterface;
use Darvin\PaymentBundle\UrlBuilder\PaymentUrlBuilderInterface;
use Omnipay\Common\Message\RedirectResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends Controller
{
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

            


        } elseif ($response->isSuccessful()) {
            $manager->markAsPaid($payment);

            return $this->redirect($urlBuilder->getSuccessUrl($payment, $gatewayName));
        } elseif ($response->isCancelled()) {
            $manager->markAsCanceled($payment);

            return $this->redirect($urlBuilder->getCanceledUrl($payment, $gatewayName));
        }
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

    public function prepareAction()
    {
        $gatewayName = 'telr';

        $storage = $this->get('payum')->getStorage(Payment::class);

        $payment = $storage->create();
        $payment->setNumber(uniqid());
        $payment->setCurrencyCode('AED');
        $payment->setTotalAmount(555); // 1.23 EUR
        $payment->setDescription('A description');
        $payment->setClientId('anId');
        $payment->setClientEmail('foo@example.com');

        $storage->update($payment);

        $captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
            $gatewayName,
            $payment,
            'done' // the route to redirect after capture
        )
        ;

        return $this->redirect($captureToken->getTargetUrl());
    }

    public function doneAction(Request $request)
    {
        $token = $this->get('payum')->getHttpRequestVerifier()->verify($request);

        $gateway = $this->get('payum')->getGateway($token->getGatewayName());

        // You can invalidate the token, so that the URL cannot be requested any more:
        // $this->get('payum')->getHttpRequestVerifier()->invalidate($token);

        // Once you have the token, you can get the payment entity from the storage directly.
        // $identity = $token->getDetails();
        // $payment = $this->get('payum')->getStorage($identity->getClass())->find($identity);

        // Or Payum can fetch the entity for you while executing a request (preferred).
        $gateway->execute($status = new GetHumanStatus($token));
        $payment = $status->getFirstModel();

        // Now you have order and payment status

        return new JsonResponse([
            'status'  => $status->getValue(),
            'payment' => [
                'total_amount'  => $payment->getTotalAmount(),
                'currency_code' => $payment->getCurrencyCode(),
                'details'       => $payment->getDetails(),
            ],
        ]
        );
    }
}
