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

use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Form\Type\GatewayRedirectType;
use Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface;
use Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface;
use Darvin\PaymentBundle\Workflow\Transitions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Workflow\WorkflowInterface;
use Twig\Environment;

/**
 * Purchase controller
 */
class PurchaseController
{
    use PaymentControllerTrait;

    /**
     * @var \Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface
     */
    private $gatewayFactory;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface
     */
    private $paymentUrlBuilder;

    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * @var \Symfony\Component\Workflow\WorkflowInterface
     */
    private $workflow;

    public function __construct(
        GatewayFactoryInterface $gatewayFactory,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        PaymentUrlBuilderInterface $paymentUrlBuilder,
        Environment $twig,
        WorkflowInterface $workflow
    ) {
        $this->gatewayFactory = $gatewayFactory;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->paymentUrlBuilder = $paymentUrlBuilder;
        $this->twig = $twig;
        $this->workflow = $workflow;
    }

    /**
     * @param string                               $gatewayName Gateway name
     * @param \Darvin\PaymentBundle\Entity\Payment $payment     Payment
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function __invoke(string $gatewayName, Payment $payment): Response
    {
        $bridge = $this->getBridge($gatewayName);
        $gateway = $this->getGateway($gatewayName);

        if (!$gateway->supportsPurchase()) {
            throw new NotFoundHttpException(sprintf("%s doesn't support purchase method", $gatewayName));
        }

        if (!$this->workflow->can($payment, Transitions::PURCHASE)) {
            throw new \LogicException('This operation is not available for your payment');
        }

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
                $this->twig->render('@DarvinPayment/Payment/purchase.html.twig', [
                    'form'     => $form->createView(),
                    'payment'  => $payment,
                    'response' => $response,
                    'gateway'  => $gateway,
                ])
            );
        }

        if ($response->isSuccessful()) {
            return new RedirectResponse($this->paymentUrlBuilder->getPurchaseSuccessUrl($payment, $gatewayName));
        }

        if ($response->isCancelled()) {
            return new RedirectResponse($this->paymentUrlBuilder->getCanceledUrl($payment, $gatewayName));
        }

        throw new \LogicException('Undefined response');
    }

    /**
     * @inheritDoc
     */
    protected function getGatewayFactory(): GatewayFactoryInterface
    {
        return $this->gatewayFactory;
    }

    /**
     * @inheritDoc
     */
    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }
}
