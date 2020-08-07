<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Manager;

use Darvin\PaymentBundle\DBAL\Type\PaymentStatusType;
use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\Event\ChangedStatusEvent;
use Darvin\PaymentBundle\Event\PaymentEvents;
use Darvin\PaymentBundle\Token\Manager\PaymentTokenManagerInterface;
use Darvin\Utils\ORM\EntityResolverInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

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
     * @param EventDispatcherInterface     $eventDispatcher
     * @param PaymentTokenManagerInterface $tokenManager
     * @param string                       $defaultCurrency
     */
    public function __construct(
        EntityManager $entityManager,
        EntityResolverInterface $entityResolver,
        EventDispatcherInterface $eventDispatcher,
        PaymentTokenManagerInterface $tokenManager,
        string $defaultCurrency
    ) {
        $this->entityManager = $entityManager;
        $this->entityResolver = $entityResolver;
        $this->eventDispatcher = $eventDispatcher;
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
    public function markAs(PaymentInterface $payment, string $status, bool $invalidateActionToken = false): void
    {
        $payment->setStatus($status);
        $this->entityManager->flush($payment);

        $this->eventDispatcher->dispatch(new ChangedStatusEvent($payment), PaymentEvents::CHANGED_STATUS);

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
