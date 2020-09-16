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
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Form\Type\GatewayRedirectType;
use Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface;
use Darvin\PaymentBundle\Redirect\RedirectFactoryInterface;
use Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface;
use Darvin\PaymentBundle\Workflow\Transitions;
use Doctrine\ORM\EntityManagerInterface;
use Omnipay\Common\GatewayInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Common functional for all payment controllers
 */
abstract class AbstractController
{
    /**
     * @var \Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface
     */
    protected $gatewayFactory;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Darvin\PaymentBundle\Redirect\RedirectFactoryInterface
     */
    protected $redirectFactory;

    /**
     * @var \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface
     */
    protected $urlBuilder;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     * @var \Symfony\Component\Workflow\WorkflowInterface
     */
    protected $workflow;

    /**
     * @var bool
     */
    protected $preAuthorize;

    /**
     * @param \Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface $gatewayFactory  Gateway factory
     * @param \Doctrine\ORM\EntityManagerInterface                          $em              Entity manager
     * @param \Symfony\Component\Form\FormFactoryInterface                  $formFactory     Form factory
     * @param \Psr\Log\LoggerInterface                                      $logger          Logger
     * @param \Darvin\PaymentBundle\Redirect\RedirectFactoryInterface       $redirectFactory Redirect factory
     * @param \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface          $urlBuilder      URL builder
     * @param \Symfony\Contracts\Translation\TranslatorInterface            $translator      Translator
     * @param \Twig\Environment                                             $twig            Twig
     * @param \Symfony\Component\Workflow\WorkflowInterface                 $workflow        Workflow
     * @param bool                                                          $preAuthorize    Pre-authorize payment enable
     */
    public function __construct(
        GatewayFactoryInterface $gatewayFactory,
        EntityManagerInterface $em,
        \Twig\Environment $twig,
        FormFactoryInterface $formFactory,
        LoggerInterface $logger,
        RedirectFactoryInterface $redirectFactory,
        PaymentUrlBuilderInterface $urlBuilder,
        TranslatorInterface $translator,
        WorkflowInterface $workflow,
        bool $preAuthorize
    ){
        $this->gatewayFactory = $gatewayFactory;
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->logger = $logger;
        $this->redirectFactory = $redirectFactory;
        $this->urlBuilder = $urlBuilder;
        $this->translator = $translator;
        $this->twig = $twig;
        $this->workflow = $workflow;
        $this->preAuthorize = $preAuthorize;
    }

    /**
     * @param string $gatewayName Gateway name
     *
     * @return \Darvin\PaymentBundle\Bridge\BridgeInterface
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getBridge(string $gatewayName): BridgeInterface
    {
        try {
            return $this->gatewayFactory->getBridge($gatewayName);
        } catch (BridgeNotExistsException $ex) {
            throw new NotFoundHttpException($ex->getMessage());
        }
    }

    /**
     * @param string $gatewayName Gateway name
     *
     * @return \Omnipay\Common\GatewayInterface
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getGateway(string $gatewayName): GatewayInterface
    {
        try {
            return $this->gatewayFactory->createGateway($gatewayName);
        } catch (BridgeNotExistsException $ex) {
            throw new NotFoundHttpException($ex->getMessage());
        }
    }

    /**
     * @param string $token Token
     *
     * @return Payment
     */
    protected function getPaymentByToken(string $token): Payment
    {
        $payment = $this->em
            ->getRepository(Payment::class)
            ->findOneBy(['actionToken' => $token]);

        if (null === $payment) {
            throw new NotFoundHttpException(sprintf('Unable to find payment with token %s', $token));
        }

        return $payment;
    }

    /**
     * @param GatewayInterface $gateway Gateway
     * @param string           $method  Name of gateway method
     */
    protected function validateGateway(GatewayInterface $gateway, string $method): void
    {
        if (!method_exists($gateway, $method)) {
            $errorMessage = $this->translator->trans('payment.log.error.not_support_gateway_method', [
                '%gateway%' => $gateway->getName(),
                '%method%'  => $method,
            ]);

            throw new NotFoundHttpException($errorMessage);
        }
    }

    /**
     * @param Payment               $payment    Payment
     * @param string                $transition Workflow transition
     * @param GatewayInterface|null $gateway    Gateway
     */
    protected function validatePayment(Payment $payment, string $transition, ?GatewayInterface $gateway = null): void
    {
        if (!$this->workflow->can($payment, $transition)) {
            $errorMessage = $this->translator->trans('payment.log.error.not_available_operation', [
                '%transition%' => $transition,
            ]);

            $this->logger->error($errorMessage, ['payment' => $payment]);

            throw new NotFoundHttpException($errorMessage);
        }

        if ($gateway !== null &&
            $payment->getGatewayName() !== null &&
            $payment->getGatewayName() !== $gateway->getName()
        ) {
            $errorMessage = $this->translator->trans('payment.log.error.wrong_gateway', [
                '%gateway%'        => $gateway->getName(),
                '%paymentGateway%' => $payment->getGatewayName(),
            ]);

            $this->logger->error($errorMessage, ['payment' => $payment]);

            throw new NotFoundHttpException($errorMessage);
        }
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment Payment
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function createErrorResponse(Payment $payment): RedirectResponse
    {
        return new RedirectResponse($this->urlBuilder->getErrorUrl($payment));
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment Payment
     *
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \LogicException
     */
    protected function createPaymentResponse(Payment $payment): Response
    {
        if (!$payment->hasRedirect()) {
            throw new \LogicException('Redirect could not be empty');
        }

        $redirect = $payment->getRedirect();

        if ($payment->getRedirect()->isExpired()) {
            $this->workflow->apply($payment, Transitions::EXPIRE);
            $this->em->flush();

            $this->logger->warning($this->translator->trans('payment.log.warning.session_expired'), ['payment' => $payment]);

            return $this->createErrorResponse($payment);
        }

        if ($redirect->getMethod() !== 'POST') {
            return new RedirectResponse($redirect->getUrl());
        }

        $form = $this->formFactory->create(GatewayRedirectType::class, $redirect->getData(), [
            'action' => $redirect->getUrl(),
            'method' => $redirect->getMethod(),
        ]);

        return new Response(
            $this->twig->render('@DarvinPayment/payment/purchase.html.twig', [
                'form' => $form->createView(),
            ])
        );
    }
}
