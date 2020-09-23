<?php declare(strict_types=1);
/**
 * @author    Alexander Volodin <mr-stanlik@yandex.ru>
 * @copyright Copyright (c) 2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\EventListener\Payment\Updated;

use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\State\Event\ChangedStateEvent;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class payment events subscriber for log about changed state
 */
class EventDispatchSubscriber implements EventSubscriber
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
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
     * @param \Doctrine\ORM\Event\OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args): void
    {
        $uow = $args->getEntityManager()->getUnitOfWork();
        foreach ($uow->getIdentityMap() as $key => $entities) {
            foreach ($entities as $entity) {
                if ($entity instanceof Payment) {
                    $this->eventDispatcher->dispatch(new ChangedStateEvent($entity));
                }
            }
        }
    }

    /**
     * @param \Doctrine\ORM\Event\PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args): void
    {
        $uow = $args->getEntityManager()->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Payment) {
                $changeSet = $uow->getEntityChangeSet($entity);
                if (isset($changeSet['state'])) {
                    $this->eventDispatcher->dispatch(new ChangedStateEvent($entity));
                }
            }
        }
    }
}
