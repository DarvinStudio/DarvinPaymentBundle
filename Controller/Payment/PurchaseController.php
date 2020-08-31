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
use Darvin\PaymentBundle\Controller\PreCheckControllerInterface;
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Form\Type\GatewayRedirectType;
use Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface;
use Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface;
use Darvin\PaymentBundle\Workflow\Transitions;
use Doctrine\ORM\EntityManagerInterface;
use Omnipay\Common\GatewayInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Workflow\WorkflowInterface;
use Twig\Environment;

/**
 * Purchase controller
 */
class PurchaseController extends AbstractController implements PreCheckControllerInterface
{
    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param GatewayFactoryInterface    $gatewayFactory    Gateway Factory
     * @param EntityManagerInterface     $entityManager     Entity Manager
     * @param Environment                $twig              Twig
     * @param FormFactoryInterface       $formFactory       Form Factory
     * @param PaymentUrlBuilderInterface $paymentUrlBuilder Url builder
     * @param WorkflowInterface          $workflow          Workflow
     */
    public function __construct(
        GatewayFactoryInterface $gatewayFactory,
        EntityManagerInterface $entityManager,
        Environment $twig,
        FormFactoryInterface $formFactory,
        PaymentUrlBuilderInterface $paymentUrlBuilder,
        WorkflowInterface $workflow
    ) {
        parent::__construct($gatewayFactory, $entityManager, $twig, $paymentUrlBuilder, $workflow);

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

        $this->preCheckPayment($gateway, $payment);

        $response = $gateway->purchase($bridge->purchaseParameters($payment))->send();

        if ($response->getTransactionReference() !== null) {
            $payment->setTransactionRef($response->getTransactionReference());
            $this->getEntityManager()->flush();
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
                $this->getTwig()->render('@DarvinPayment/payment/purchase.html.twig', [
                    'form'     => $form->createView(),
                    'payment'  => $payment,
                    'response' => $response,
                    'gateway'  => $gateway,
                ])
            );
        }

        if ($response->isSuccessful()) {
            return new RedirectResponse($this->getPaymentUrlBuilder()->getPurchaseSuccessUrl($payment, $gatewayName));
        }

        if ($response->isCancelled()) {
            return new RedirectResponse($this->getPaymentUrlBuilder()->getCanceledUrl($payment, $gatewayName));
        }

        $errorMessage = sprintf('Can\'t handler response for payment id %s and gateway %s', $payment->getId(), $gatewayName);

        if (null !== $this->getLogger()) {
            $this->getLogger()->error($errorMessage);

            return new RedirectResponse($this->getPaymentUrlBuilder()->getFailedUrl($payment, $gatewayName));
        }

        throw new \LogicException($errorMessage);
    }

    /**
     * @inheritDoc
     */
    public function preCheckPayment(GatewayInterface $gateway, Payment $payment): void
    {
        if (!$gateway->supportsPurchase()) {
            $errorMessage = sprintf('Payment gateway %s doesn\'t support purchase method', $gateway->getName());
            if (null !== $this->getLogger()) {
                $this->getLogger()->error($errorMessage);
            }

            throw new NotFoundHttpException($errorMessage);
        }

        if (!$this->getWorkflow()->can($payment, Transitions::PURCHASE)) {
            $errorMessage = 'This operation is not available for your payment';

            if (null !== $this->getLogger()) {
                $this->getLogger()->error($errorMessage);
            }

            throw new \LogicException($errorMessage);
        }
    }
}
