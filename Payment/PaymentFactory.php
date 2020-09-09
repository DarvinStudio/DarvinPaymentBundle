<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Payment;

use Darvin\PaymentBundle\Entity\Payment;
use Darvin\Utils\ORM\EntityResolverInterface;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Payment factory
 */
class PaymentFactory implements PaymentFactoryInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var \Darvin\Utils\ORM\EntityResolverInterface
     */
    protected $entityResolver;

    /**
     * @var \Symfony\Component\Workflow\WorkflowInterface
     */
    protected $workflow;

    /**
     * @var string
     */
    protected $defaultCurrency;

    /**
     * @var bool
     */
    protected $autoApproval;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface      $entityManager   Entity manager
     * @param \Darvin\Utils\ORM\EntityResolverInterface $entityResolver  Entity resolver
     * @param WorkflowInterface                         $workflow        Workflow for payment state
     * @param string                                    $defaultCurrency Currency by default
     * @param bool                                      $autoApproval    Auto approval or need admin approval
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EntityResolverInterface $entityResolver,
        WorkflowInterface $workflow,
        string $defaultCurrency,
        bool $autoApproval
    ) {
        $this->entityManager = $entityManager;
        $this->entityResolver = $entityResolver;
        $this->workflow = $workflow;
        $this->defaultCurrency = $defaultCurrency;
        $this->autoApproval = $autoApproval;
    }

    /**
     * @inheritdoc
     */
    public function createPayment(
        string $orderId,
        string $orderEntityClass,
        string $orderNumber,
        string $amount,
        ?string $currencyCode,
        ?string $clientId,
        ?string $clientEmail
    ): Payment {
        $class = $this->entityResolver->resolve(Payment::class);

        /** @var \Darvin\PaymentBundle\Entity\Payment $payment */
        $payment = new $class;

        $payment
            ->setOrderId($orderId)
            ->setOrderEntityClass($orderEntityClass)
            ->setOrderNumber($orderNumber)
            ->setAmount($amount)
            ->setClientId($clientId)
            ->setClientEmail($clientEmail)
            ->setCurrencyCode($currencyCode ?? $this->defaultCurrency)
            ->setActionToken(Uuid::uuid4()->toString());

        if ($this->autoApproval) {
            $this->workflow->apply($payment, 'approve');
        }

        return $payment;
    }
}
