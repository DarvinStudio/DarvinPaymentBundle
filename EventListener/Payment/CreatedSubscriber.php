<?php declare(strict_types=1);
/**
 * @author    Alexander Volodin <mr-stanlik@yandex.ru>
 * @copyright Copyright (c) 2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\EventListener\Payment;

use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Event\State\ChangedEvent;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Subscriber for create event for new payment
 */
class CreatedSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher Event dispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::postPersist => 'createEvent',
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    private function createEvent(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Payment) {
            $this->eventDispatcher->dispatch(new ChangedEvent($entity));
        }

    }
}
