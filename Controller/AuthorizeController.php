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
use Darvin\PaymentBundle\State\Manager\StateManagerInterface;
use Darvin\PaymentBundle\UrlBuilder\PaymentUrlBuilderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

/**
 * Purchase controller
 */
class AuthorizeController
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
     * @var \Darvin\PaymentBundle\State\Manager\StateManagerInterface
     */
    private $stateManager;

    /**
     * @var \Darvin\PaymentBundle\UrlBuilder\PaymentUrlBuilderInterface
     */
    private $paymentUrlBuilder;

    /**
     * @var \Twig\Environment
     */
    private $twig;

    public function __construct(
        GatewayFactoryInterface $gatewayFactory,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        StateManagerInterface $stateManager,
        PaymentUrlBuilderInterface $paymentUrlBuilder,
        Environment $twig
    ) {
        $this->gatewayFactory = $gatewayFactory;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->stateManager = $stateManager;
        $this->paymentUrlBuilder = $paymentUrlBuilder;
        $this->twig = $twig;
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

        if (!$gateway->supportsAuthorize()) {
            throw new NotFoundHttpException(sprintf("%s doesn't support authorize method", $gatewayName));
        }

        $this->stateManager->markAsPending($payment);

        $response = $gateway->authorize($bridge->authorizeParameters($payment))->send();

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
            $this->stateManager->markAsCompleted($payment);

            return new RedirectResponse($this->paymentUrlBuilder->getSuccessUrl($payment, $gatewayName));
        }

        if ($response->isCancelled()) {
            $this->stateManager->markAsCanceled($payment);

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
