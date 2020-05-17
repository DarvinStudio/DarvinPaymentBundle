<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.07.2018
 * Time: 21:07
 */

namespace Darvin\PaymentBundle\PaymentManager;

use Darvin\PaymentBundle\DBAL\Type\PaymentStatusType;
use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\Token\Manager\PaymentTokenManagerInterface;
use Darvin\Utils\ORM\EntityResolverInterface;
use Doctrine\ORM\EntityManager;

class PaymentManager implements PaymentManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var \Darvin\Utils\ORM\EntityResolverInterface
     */
    protected $entityResolver;

    /**
     * @var PaymentTokenManagerInterface
     */
    protected $tokenManager;

    /**
     * @var string
     */
    protected $defaultCurrency;

    /**
     * PaymentManager constructor.
     *
     * @param EntityManager                $entityManager
     * @param EntityResolverInterface      $entityResolver
     * @param PaymentTokenManagerInterface $tokenManager
     * @param string                       $defaultCurrency
     */
    public function __construct(
        EntityManager $entityManager,
        EntityResolverInterface $entityResolver,
        PaymentTokenManagerInterface $tokenManager,
        string $defaultCurrency
    ) {
        $this->entityManager = $entityManager;
        $this->entityResolver = $entityResolver;
        $this->tokenManager = $tokenManager;
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * @inheritdoc
     */
    public function create(
        int $orderId,
        string $orderEntityClass,
        string $amount,
        string $currencyCode,
        $clientId = null,
        ?string $clientEmail = null,
        ?string $description = null,
        ?array $options = []
    ) {
        $class = $this->entityResolver->resolve(PaymentInterface::class);

        /** @var \Darvin\PaymentBundle\Entity\Payment $object */
        $object = new $class();
        $object
            ->setOrderId($orderId)
            ->setOrderEntityClass($orderEntityClass)
            ->setAmount($amount)
            ->setCurrencyCode($currencyCode ?? $this->defaultCurrency)
            ->setClientId($clientId)
            ->setClientEmail($clientEmail)
            ->setDescription($description);

        $this->entityManager->persist($object);
        $this->entityManager->flush($object);

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function markAsNew(PaymentInterface $payment): void
    {
        $this->markAs($payment, PaymentStatusType::NEW);
    }

    /**
     * @inheritDoc
     */
    public function markAsPending(PaymentInterface $payment): void
    {
        $this->markAs($payment, PaymentStatusType::PENDING);
        $this->tokenManager->attach($payment);
    }

    /**
     * @inheritDoc
     */
    public function markAsPaid(PaymentInterface $payment): void
    {
        $this->markAs($payment, PaymentStatusType::PAID, true);
    }

    /**
     * @inheritDoc
     */
    public function markAsCanceled(PaymentInterface $payment): void
    {
        $this->markAs($payment, PaymentStatusType::CANCELED, true);
    }

    /**
     * @inheritDoc
     */
    public function markAsFailed(PaymentInterface $payment): void
    {
        $this->markAs($payment, PaymentStatusType::FAILED, true);
    }

    /**
     * @inheritDoc
     */
    public function markAsRefund(PaymentInterface $payment): void
    {
        $this->markAs($payment, PaymentStatusType::REFUND);
    }

    /**
     * @inheritDoc
     */
    public function markAs(PaymentInterface $payment, $status, $invalidateActionToken = false): void
    {
        $payment->setStatus($status);
        $this->entityManager->flush($payment);

        if ($invalidateActionToken) {
            $this->tokenManager->invalidate($payment);
        }
    }

    /**
     * @inheritDoc
     */
    public function setTransactionReference(PaymentInterface $payment, string $reference): void
    {
        $payment->setTransactionRef($reference);
        $this->entityManager->flush($payment);
    }

    /**
     * @inheritDoc
     */
    public function findById($id): ?PaymentInterface
    {
        return $this->entityManager->find(PaymentInterface::class, $id);
    }
}
