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
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityRepository;

/**
 * Event entity repository
 */
class EventRepository extends EntityRepository
{
    /**
     * @param \Darvin\PaymentBundle\Entity\Event $event Event
     *
     * @throws \LogicException
     */
    public function add(Event $event): void
    {
        if (null === $event->getPayment()->getId()) {
            throw new \LogicException('Payment ID missing');
        }

        $table = $this->getClassMetadata()->getTableName();

        $this->getEntityManager()->getConnection()->executeUpdate(<<<UPDATE
INSERT INTO $table (payment_id, level, message, created_at)
VALUES (:payment_id, :level, :message, :created_at)
UPDATE
            ,
            [
                'payment_id' => $event->getPayment()->getId(),
                'level'      => $event->getLevel(),
                'message'    => $event->getMessage(),
                'created_at' => $event->getCreatedAt(),
            ],
            [
                'payment_id' => Types::INTEGER,
                'level'      => Types::STRING,
                'message'    => Types::TEXT,
                'created_at' => Types::DATETIME_MUTABLE,
            ]
        );
    }
}
