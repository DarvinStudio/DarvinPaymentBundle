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

use Darvin\PaymentBundle\DBAL\Type\PaymentStateType;
use Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface;
use Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface;
use Darvin\PaymentBundle\Workflow\Transitions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Workflow\WorkflowInterface;
use Twig\Environment;

/**
 * Authorize success controller
 */
class AuthorizeSuccessController
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
        PaymentUrlBuilderInterface $paymentUrlBuilder,
        Environment $twig,
        WorkflowInterface $workflow
    ) {
        $this->gatewayFactory = $gatewayFactory;
        $this->entityManager = $entityManager;
        $this->paymentUrlBuilder = $paymentUrlBuilder;
        $this->twig = $twig;
        $this->workflow = $workflow;
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

        if ($payment->getState() === PaymentStateType::AUTHORIZED) {
            return new Response(
                $this->twig->render('@DarvinPayment/Payment/authorize_success.html.twig', [
                    'payment' => $payment,
                ])
            );
        }

        if (!$this->workflow->can($payment, Transitions::AUTHORIZE)) {
            throw new \LogicException('This operation is not available for your payment');
        }

        if (!$gateway->supportsCompleteAuthorize()) {
            throw new NotFoundHttpException(sprintf('Gateway "%s" doesn\'t support "completePurchase" method', $gatewayName));
        }

        $response = $gateway->completeAuthorize($bridge->completeAuthorizeParameters($payment))->send();

        if ($response->isSuccessful()) {
            $this->workflow->apply($payment, Transitions::AUTHORIZE);
            $this->entityManager->flush();

            return new Response(
                $this->twig->render('@DarvinPayment/Payment/authorize_success.html.twig', [
                    'payment' => $payment,
                ])
            );
        }

        return new RedirectResponse($this->paymentUrlBuilder->getFailedUrl($payment, $gatewayName));
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
