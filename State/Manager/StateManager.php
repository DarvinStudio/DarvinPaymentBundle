<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\State\Manager;

use Darvin\PaymentBundle\DBAL\Type\PaymentStateType;
use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\Event\State\ChangedEvent;
use Darvin\PaymentBundle\Event\State\StateEvents;
use Darvin\PaymentBundle\Token\Manager\PaymentTokenManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class StateManager implements StateManagerInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var \Darvin\PaymentBundle\Token\Manager\PaymentTokenManagerInterface
     */
    protected $tokenManager;

    /**
     * PaymentManager constructor.
     *
     * @param EntityManagerInterface       $entityManager   Entity manager
     * @param EventDispatcherInterface     $eventDispatcher Event dispatcher
     * @param PaymentTokenManagerInterface $tokenManager    Payment token manager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        PaymentTokenManagerInterface $tokenManager
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenManager = $tokenManager;
    }

    /**
     * @inheritDoc
     */
    public function markAsNew(PaymentInterface $payment): void
    {
        $this->markAs($payment, PaymentStateType::NEW);
    }

    /**
     * @inheritDoc
     */
    public function markAsPending(PaymentInterface $payment): void
    {
        $this->markAs($payment, PaymentStateType::PENDING);
        $this->tokenManager->attach($payment);
    }

    /**
     * @inheritDoc
     */
    public function markAsCompleted(PaymentInterface $payment): void
    {
        $this->markAs($payment, PaymentStateType::COMPLETED, true);
    }

    /**
     * @inheritDoc
     */
    public function markAsCanceled(PaymentInterface $payment): void
    {
        $this->markAs($payment, PaymentStateType::CANCELED, true);
    }

    /**
     * @inheritDoc
     */
    public function markAsFailed(PaymentInterface $payment): void
    {
        $this->markAs($payment, PaymentStateType::FAILED, true);
    }

    /**
     * @inheritDoc
     */
    public function markAsRefund(PaymentInterface $payment): void
    {
        $this->markAs($payment, PaymentStateType::REFUND);
    }

    /**
     * @param PaymentInterface $payment
     * @param string           $state
     *
     * @return void
     */
    public function markAs(PaymentInterface $payment, string $state, bool $invalidateActionToken = false): void
    {
        $prevState = $payment->getState();
        $payment->setState($state);
        $this->entityManager->flush($payment);

        $this->eventDispatcher->dispatch(new ChangedEvent($payment, $prevState), StateEvents::CHANGED);

        if ($invalidateActionToken) {
            $this->tokenManager->invalidate($payment);
        }
    }

    public function isCompleted(PaymentInterface $payment): bool
    {
        return PaymentStateType::COMPLETED === $payment->getState();
    }
}
