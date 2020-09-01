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

/**
 * Purchase controller
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
    protected $entityManager;

    /**
     * @var \Psr\Log\LoggerInterface|null
     */
    protected $logger;

    /**
     * @var \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface
     */
    protected $urlBuilder;

    /**
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     * @var \Symfony\Component\Workflow\WorkflowInterface
     */
    protected $workflow;

    /**
     * @param \Darvin\PaymentBundle\Gateway\Factory\GatewayFactoryInterface $gatewayFactory
     */
    public function setGatewayFactory(GatewayFactoryInterface $gatewayFactory): void
    {
        $this->gatewayFactory = $gatewayFactory;
    }

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function setLogger(?LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface $urlBuilder
     */
    public function setUrlBuilder(PaymentUrlBuilderInterface $urlBuilder): void
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param \Twig\Environment $twig
     */
    public function setTwig(\Twig\Environment $twig): void
    {
        $this->twig = $twig;
    }

    /**
     * @param \Symfony\Component\Workflow\WorkflowInterface $workflow
     */
    public function setWorkflow(WorkflowInterface $workflow): void
    {
        $this->workflow = $workflow;
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
            $this->addErrorLog($ex->getMessage());

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
            $this->addErrorLog($ex->getMessage());

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
        $payment = $this->entityManager
            ->getRepository(Payment::class)
            ->findOneBy(['actionToken' => $token]);

        if (null === $payment) {
            $errorMessage = sprintf('Unable to find payment with token %s', $token);

            $this->addErrorLog($errorMessage);

            throw new NotFoundHttpException($errorMessage);
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
            $errorMessage = sprintf('Payment gateway "%s" doesn\'t support "%s" method', $gateway->getName(), $method);

            $this->addErrorLog($errorMessage);

            throw new NotFoundHttpException($errorMessage);
        }
    }

    /**
     * @param Payment $payment    Payment
     * @param string  $transition Workflow transition
     */
    protected function validatePayment(Payment $payment, string $transition): void
    {
        if (!$this->workflow->can($payment, $transition)) {
            $errorMessage = 'This operation is not available for your payment';

            $this->addErrorLog($errorMessage);

            throw new NotFoundHttpException($errorMessage);
        }
    }

    /**
     * @param string $message Log message
     */
    protected function addErrorLog(string $message): void
    {
        if (null !== $this->logger) {
            $this->logger->error($message);
        }
    }
}
