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

use Darvin\PaymentBundle\Entity\Log;
use Doctrine\ORM\EntityRepository;

/**
 * Log Repository
 */
class LogRepository extends EntityRepository
{
    /**
     * @param \Darvin\PaymentBundle\Entity\Log $log       Log
     * @param int                              $paymentId Payment Id
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    public function saveLog(Log $log, int $paymentId): void
    {
        $className = (new \ReflectionClass($log))->getShortName();

        $this->getEntityManager()->getConnection()->executeUpdate('
            INSERT INTO payment_log (payment_id, level, message, created_at, dtype) 
            VALUES (:payment_id, :level, :message, :created_at, :dtype)',
            [
                'payment_id' => $paymentId,
                'level'      => $log->getLevel(),
                'message'    => $log->getMessage(),
                'created_at' => $log->getCreatedAt(),
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
