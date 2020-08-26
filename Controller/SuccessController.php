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

use Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface;
use Darvin\PaymentBundle\State\Manager\StateManagerInterface;
use Darvin\PaymentBundle\UrlBuilder\PaymentUrlBuilderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

/**
 * Success controller
 */
class SuccessController
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
        StateManagerInterface $stateManager,
        PaymentUrlBuilderInterface $paymentUrlBuilder,
        Environment $twig
    ) {
        $this->gatewayFactory = $gatewayFactory;
        $this->entityManager = $entityManager;
        $this->stateManager = $stateManager;
        $this->paymentUrlBuilder = $paymentUrlBuilder;
        $this->twig = $twig;
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

        if ($this->stateManager->isCompleted($payment)) {
            return new Response(
                $this->twig->render('@DarvinPayment/Payment/success.html.twig', [
                    'payment' => $payment,
                ])
            );
        }

        if (!$gateway->supportsCompletePurchase()) {
            throw new NotFoundHttpException(sprintf('Gateway "%s" doesn\'t support "completePurchase" method', $gatewayName));
        }

        $response = $gateway->completePurchase($bridge->completePurchaseParameters($payment))->send();

        if ($response->isSuccessful()) {
            $this->stateManager->markAsCompleted($payment);

            return new Response(
                $this->twig->render('@DarvinPayment/Payment/success.html.twig', [
                    'payment' => $payment,
                ])
            );
        }

        $this->stateManager->markAsFailed($payment);

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
