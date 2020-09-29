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
use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface;
use Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Omnipay\Common\GatewayInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

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
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

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
     * @param \Psr\Log\LoggerInterface                                      $logger          Logger
     * @param \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface          $urlBuilder      URL builder
     * @param \Symfony\Contracts\Translation\TranslatorInterface            $translator      Translator
     * @param \Twig\Environment                                             $twig            Twig
     * @param \Symfony\Component\Workflow\WorkflowInterface                 $workflow        Workflow
     * @param bool                                                          $preAuthorize    Pre-authorize payment enable
     */
    public function __construct(
        GatewayFactoryInterface $gatewayFactory,
        EntityManagerInterface $em,
        Environment $twig,
        LoggerInterface $logger,
        PaymentUrlBuilderInterface $urlBuilder,
        TranslatorInterface $translator,
        WorkflowInterface $workflow,
        bool $preAuthorize
    ) {
        $this->gatewayFactory = $gatewayFactory;
        $this->em = $em;
        $this->logger = $logger;
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
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    final protected function getBridge(string $gatewayName): BridgeInterface
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
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    final protected function getGateway(string $gatewayName): GatewayInterface
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
     * @return \Darvin\PaymentBundle\Entity\Payment
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    final protected function getPaymentByToken(string $token): Payment
    {
        $payment = $this->em->getRepository(PaymentInterface::class)->findOneBy(['token' => $token]);

        if (null === $payment) {
            throw new NotFoundHttpException(sprintf('Unable to find payment with token %s', $token));
        }

        return $payment;
    }

    /**
     * @param \Omnipay\Common\GatewayInterface $gateway Gateway
     * @param string                           $method  Name of gateway method
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    final protected function validateGateway(GatewayInterface $gateway, string $method): void
    {
        if (!method_exists($gateway, $method)) {
            $errorMessage = $this->translator->trans('error.not_support_gateway_method', [
                '%gateway%' => $gateway->getName(),
                '%method%'  => $method,
            ], 'payment_event');

            throw new NotFoundHttpException($errorMessage);
        }
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment     Payment
     * @param string                               $operation   Workflow operation
     * @param string|null                          $gatewayName Current gateway Name
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    final protected function validatePayment(Payment $payment, string $operation, ?string $gatewayName = null): void
    {
        if (!$this->workflow->can($payment, $operation)) {
            $errorMessage = $this->translator->trans('error.not_available_operation', [
                '%operation%' => $operation,
            ], 'payment_event');

            $this->logger->error($errorMessage, ['payment' => $payment]);

            throw new NotFoundHttpException($errorMessage);
        }

        if ($gatewayName !== null
            && $payment->getGateway() !== null
            && $payment->getGateway() !== $gatewayName
        ) {
            $errorMessage = $this->translator->trans('error.wrong_gateway', [
                '%gateway%'        => $gatewayName,
                '%paymentGateway%' => $payment->getGateway(),
            ], 'payment_event');

            $this->logger->error($errorMessage, ['payment' => $payment]);

            throw new NotFoundHttpException($errorMessage);
        }
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment Payment
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    final protected function createErrorResponse(Payment $payment): RedirectResponse
    {
        return new RedirectResponse($this->urlBuilder->getErrorUrl($payment));
    }
}
