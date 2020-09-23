<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Repository;

use Darvin\PaymentBundle\Entity\Event;
use Doctrine\ORM\EntityRepository;

/**
 * Event Repository
 */
class EventRepository extends EntityRepository
{
    /**
     * @param \Darvin\PaymentBundle\Entity\Event $event Event
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    public function save(Event $event): void
    {
        $className = (new \ReflectionClass($event))->getShortName();

        if (null === $event->getPayment()->getId()) {
            throw new \LogicException('Payment ID missing');
        }

        $this->getEntityManager()->getConnection()->executeUpdate('
            INSERT INTO payment_event (payment_id, level, message, created_at, dtype) 
            VALUES (:payment_id, :level, :message, :created_at, :dtype)',
            [
                'payment_id' => $event->getPayment()->getId(),
                'level'      => $event->getLevel(),
                'message'    => $event->getMessage(),
                'created_at' => $event->getCreatedAt(),
                'dtype'      => mb_strtolower($className),
            ], [
                'payment_id' => \Doctrine\DBAL\Types\Types::INTEGER,
                'level'      => \Doctrine\DBAL\Types\Types::STRING,
                'message'    => \Doctrine\DBAL\Types\Types::TEXT,
                'created_at' => \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE,
                'dtype'      => \Doctrine\DBAL\Types\Types::STRING,
            ]
        );
    }
}
