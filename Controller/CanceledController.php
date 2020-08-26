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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * Controller for the canceling payment
 */
class CanceledController
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
     * @var \Twig\Environment
     */
    private $twig;

    public function __construct(
        GatewayFactoryInterface $gatewayFactory,
        EntityManagerInterface $entityManager,
        StateManagerInterface $stateManager,
        Environment $twig
    ) {
        $this->gatewayFactory = $gatewayFactory;
        $this->entityManager = $entityManager;
        $this->stateManager = $stateManager;
        $this->twig = $twig;
    }

    /**
     * @param string $gatewayName Gateway name
     * @param string $token       Payment token
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function __invoke(string $gatewayName, string $token): Response
    {
        $payment = $this->getPaymentByToken($token);

        $gateway = $this->getGateway($gatewayName);

        $this->stateManager->markAsCanceled($payment);

        return new Response(
            $this->twig->render('@DarvinPayment/Payment/canceled.html.twig', [
                'payment' => $payment,
                'gateway' => $gateway,
            ])
        );
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
