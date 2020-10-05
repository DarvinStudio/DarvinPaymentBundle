<?php declare(strict_types=1);
/**
 * @author    Alexander Volodin <mr-stanlik@yandex.ru>
 * @copyright Copyright (c) 2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\EventListener\State\Changed;

use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Event\State\ChangedEvent;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class payment events subscriber for log about changed state
 */
class TriggerEventSubscriber implements EventSubscriber
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var \Darvin\PaymentBundle\Event\State\ChangedEvent[]
     */
    private $events;

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher Event dispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        $this->events = [];
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
            Events::postFlush,
        ];
    }

    /**
     * @param \Doctrine\ORM\Event\OnFlushEventArgs $args Event arguments
     */
    public function onFlush(OnFlushEventArgs $args): void
    {
        $uow = $args->getEntityManager()->getUnitOfWork();

        foreach (array_merge($uow->getScheduledEntityInsertions(), $uow->getScheduledEntityUpdates()) as $entity) {
            if ($entity instanceof Payment) {
                $changeSet = $uow->getEntityChangeSet($entity);

                if (isset($changeSet['state'])) {
                    $this->events[] = new ChangedEvent($entity);
                }
            }
        }
    }

    public function postFlush(): void
    {
        foreach ($this->events as $key => $event) {
            $this->eventDispatcher->dispatch($event);

            unset($this->events[$key]);
        }
    }
}
