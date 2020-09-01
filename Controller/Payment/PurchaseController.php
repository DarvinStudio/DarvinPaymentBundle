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
use Darvin\PaymentBundle\Form\Type\GatewayRedirectType;
use Darvin\PaymentBundle\Workflow\Transitions;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Purchase controller
 */
class PurchaseController extends AbstractController
{
    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param FormFactoryInterface $formFactory Form Factory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param string $gatewayName Gateway name
     * @param string $token       Payment token
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function __invoke(string $gatewayName, string $token): Response
    {
        $bridge = $this->getBridge($gatewayName);
        $gateway = $this->getGateway($gatewayName);
        $payment = $this->getPaymentByToken($token);

        $this->validateGateway($gateway, 'purchase');
        $this->validatePayment($payment, Transitions::PURCHASE);

        try {
            $response = $gateway->purchase($bridge->purchaseParameters($payment))->send();
        } catch (\Exception $ex) {
            $this->addErrorLog(sprintf('%s: %s', __METHOD__, $ex->getMessage()));

            return new RedirectResponse($this->urlBuilder->getFailUrl($payment, $gatewayName));
        }

        if ($response->getTransactionReference() !== null) {
            $payment->setTransactionReference($response->getTransactionReference());
            $this->entityManager->flush();
        }

        if ($response->isRedirect()) {
            if ($response->getRedirectMethod() !== 'POST') {
                return new RedirectResponse($response->getRedirectUrl());
            }

            $form = $this->formFactory->create(GatewayRedirectType::class, $response->getRedirectData(), [
                'action' => $response->getRedirectUrl(),
                'method' => $response->getRedirectMethod(),
            ]);

            return new Response(
                $this->twig->render('@DarvinPayment/payment/purchase.html.twig', [
                    'form'     => $form->createView(),
                    'payment'  => $payment,
                    'response' => $response,
                    'gateway'  => $gateway,
                ])
            );
        }

        if ($response->isSuccessful()) {
            return new RedirectResponse($this->urlBuilder->getCompletePurchaseUrl($payment, $gatewayName));
        }

        if ($response->isCancelled()) {
            return new RedirectResponse($this->urlBuilder->getCancelUrl($payment, $gatewayName));
        }

        $errorMessage = sprintf(
            '%s: Can\'t handler response for payment id %s and gateway %s. Response code: %s. Response message: %s',
            __METHOD__,
            $payment->getId(),
            $gatewayName,
            $response->getCode(),
            $response->getMessage()
        );

        $this->addErrorLog($errorMessage);

        // TODO Думаю надо сохранять информацию об ошибке

        return new RedirectResponse($this->urlBuilder->getFailUrl($payment, $gatewayName));
    }
}
