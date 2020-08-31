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
use Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface;
use Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Omnipay\Common\GatewayInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Workflow\WorkflowInterface;
use Twig\Environment;

/**
 * Purchase controller
 */
abstract class AbstractController
{
    /**
     * @var \Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface
     */
    private $gatewayFactory;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

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

    /**
     * @param GatewayFactoryInterface    $gatewayFactory    Gateway factory
     * @param EntityManagerInterface     $entityManager     Entity manager
     * @param Environment                $twig              Twig
     * @param PaymentUrlBuilderInterface $paymentUrlBuilder Url builder
     * @param WorkflowInterface          $workflow          Workflow of payment state
     */
    public function __construct(
        GatewayFactoryInterface $gatewayFactory,
        EntityManagerInterface $entityManager,
        Environment $twig,
        PaymentUrlBuilderInterface $paymentUrlBuilder,
        WorkflowInterface $workflow
    ) {
        $this->gatewayFactory = $gatewayFactory;
        $this->entityManager = $entityManager;
        $this->paymentUrlBuilder = $paymentUrlBuilder;
        $this->twig = $twig;
        $this->workflow = $workflow;
    }

    /**
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function setLogger(?LoggerInterface $logger): void
    {
        $this->logger = $logger;
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
            return $this->getGatewayFactory()->getBridge($gatewayName);
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
            return $this->getGatewayFactory()->createGateway($gatewayName);
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
        $payment = $this
            ->getEntityManager()
            ->getRepository(Payment::class)
            ->findOneBy(['actionToken' => $token]);

        if (null === $payment) {
            throw new NotFoundHttpException(sprintf('Unable to find payment with token %s', $token));
        }

        return $payment;
    }

    /**
     * @return \Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface
     */
    protected function getGatewayFactory(): GatewayFactoryInterface
    {
        return $this->gatewayFactory;
    }

    /**
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @return \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface
     */
    protected function getPaymentUrlBuilder(): PaymentUrlBuilderInterface
    {
        return $this->paymentUrlBuilder;
    }

    /**
     * @return \Twig\Environment
     */
    protected function getTwig(): Environment
    {
        return $this->twig;
    }

    /**
     * @return \Symfony\Component\Workflow\WorkflowInterface
     */
    protected function getWorkflow(): WorkflowInterface
    {
        return $this->workflow;
    }

    /**
     * @return \Psr\Log\LoggerInterface|null
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }
}
