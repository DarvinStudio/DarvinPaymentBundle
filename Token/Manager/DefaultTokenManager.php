<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 08.07.2018
 * Time: 2:40
 */

namespace Darvin\PaymentBundle\Token\Manager;


use Darvin\PaymentBundle\Entity\PaymentInterface;
use Doctrine\ORM\EntityManager;

class DefaultTokenManager implements TokenManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * DefaultTokenManager constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function createActionToken(PaymentInterface $payment)
    {
        $token = md5(sprintf("%d:%d", $payment->getId(), time()));
        $payment->setActionToken($token);

        $this->entityManager->flush($payment);
    }

    /**
     * @inheritDoc
     */
    public function invalidateActionToken(PaymentInterface $payment)
    {
        $payment->setActionToken(null);
        $this->entityManager->flush($payment);
    }

    /**
     * @inheritDoc
     */
    public function findPayment($token)
    {
        return $this->entityManager->getRepository(PaymentInterface::class)->findByActionToken($token);
    }
}