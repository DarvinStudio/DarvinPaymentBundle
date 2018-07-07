<?php
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
use Doctrine\ORM\EntityManager;

class DefaultPaymentManager implements PaymentManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $paymentClass;

    /**
     * @var string
     */
    protected $defaultCurrency;

    /**
     * DefaultPaymentFactory constructor.
     *
     * @param EntityManager $entityManager
     * @param string        $paymentClass
     * @param string        $defaultCurrency
     */
    public function __construct(EntityManager $entityManager, $paymentClass, $defaultCurrency)
    {
        $this->entityManager = $entityManager;
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
        if (!is_a($this->paymentClass, Payment::class)) {
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
    }

    /**
     * @inheritDoc
     */
    public function markAsPaid(PaymentInterface $payment)
    {
        $this->markAs($payment, PaymentStatusType::PAID);
    }

    /**
     * @inheritDoc
     */
    public function markAsCanceled(PaymentInterface $payment)
    {
        $this->markAs($payment, PaymentStatusType::CANCELED);
    }

    /**
     * @inheritDoc
     */
    public function markAsFailed(PaymentInterface $payment)
    {
        $this->markAs($payment, PaymentStatusType::FAILED);
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
    public function markAs(PaymentInterface $payment, $status)
    {
        $payment->setStatus($status);
        $this->entityManager->flush($payment);
    }
}