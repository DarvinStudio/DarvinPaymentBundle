<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.07.2018
 * Time: 21:07
 */

namespace Darvin\PaymentBundle\PaymentManager;

use Darvin\PaymentBundle\DBAL\Type\PaymentStatusType;
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\Token\Manager\TokenManagerInterface;
use Doctrine\ORM\EntityManager;

class DefaultPaymentManager implements PaymentManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var TokenManagerInterface
     */
    protected $tokenManager;

    /**
     * @var string
     */
    protected $paymentClass;

    /**
     * @var string
     */
    protected $defaultCurrency;

    /**
     * DefaultPaymentManager constructor.
     *
     * @param EntityManager $entityManager
     * @param TokenManagerInterface $tokenManager
     * @param string $paymentClass
     * @param string $defaultCurrency
     */
    public function __construct(
        EntityManager $entityManager,
        TokenManagerInterface $tokenManager,
        $paymentClass,
        $defaultCurrency
    ) {
        $this->entityManager = $entityManager;
        $this->tokenManager = $tokenManager;
        $this->paymentClass = $paymentClass;
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * @inheritdoc
     */
    public function create(
        $orderId,
        $orderEntityClass,
        $amount,
        $currencyCode = null,
        $clientId = null,
        $clientEmail = null,
        $description = null,
        array $options = []
    ) {
        if (!is_a($this->paymentClass, Payment::class, true)) {
            throw new \RuntimeException(sprintf(
                "%s doesn't know how to create object of class %s",
                self::class,
                $this->paymentClass
            ));
        }

        $reflection = new \ReflectionClass($this->paymentClass);
        if ($reflection->getConstructor()->getNumberOfRequiredParameters()>0) {
            throw new \RuntimeException(sprintf(
                "Constructor of %s must have only non-required pa",
                self::class,
                $this->paymentClass
            ));
        }

        /** @var Payment $object */
        $object = $reflection->newInstance();
        $object->setOrderId($orderId);
        $object->setOrderEntityClass($orderEntityClass);
        $object->setAmount($amount);
        $object->setCurrencyCode($currencyCode ? : $this->defaultCurrency);
        $object->setClientId($clientId);
        $object->setClientEmail($clientEmail);
        $object->setDescription($description);

        $this->entityManager->persist($object);
        $this->entityManager->flush($object);

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function markAsNew(PaymentInterface $payment)
    {
        $this->markAs($payment, PaymentStatusType::NEW);
    }

    /**
     * @inheritDoc
     */
    public function markAsPending(PaymentInterface $payment)
    {
        $this->markAs($payment, PaymentStatusType::PENDING);
        $this->tokenManager->createActionToken($payment);
    }

    /**
     * @inheritDoc
     */
    public function markAsPaid(PaymentInterface $payment)
    {
        $this->markAs($payment, PaymentStatusType::PAID, true);
    }

    /**
     * @inheritDoc
     */
    public function markAsCanceled(PaymentInterface $payment)
    {
        $this->markAs($payment, PaymentStatusType::CANCELED, true);
    }

    /**
     * @inheritDoc
     */
    public function markAsFailed(PaymentInterface $payment)
    {
        $this->markAs($payment, PaymentStatusType::FAILED, true);
    }

    /**
     * @inheritDoc
     */
    public function markAsRefund(PaymentInterface $payment)
    {
        $this->markAs($payment, PaymentStatusType::REFUND);
    }

    /**
     * @inheritDoc
     */
    public function markAs(PaymentInterface $payment, $status, $invalidateActionToken = false)
    {
        $payment->setStatus($status);
        $this->entityManager->flush($payment);

        if ($invalidateActionToken) {
            $this->tokenManager->invalidateActionToken($payment);
        }
    }

    /**
     * @inheritDoc
     */
    public function setTransactionReference(PaymentInterface $payment, $reference)
    {
        $payment->setTransactionRef($reference);
        $this->entityManager->flush($payment);
    }

    /**
     * @inheritDoc
     */
    public function findById($id)
    {
        return $this->entityManager->find(PaymentInterface::class, $id);
    }


}
