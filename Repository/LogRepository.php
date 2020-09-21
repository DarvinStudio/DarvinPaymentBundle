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
use Darvin\PaymentBundle\Entity\Payment;
use Doctrine\ORM\EntityRepository;

/**
 * Log Repository
 */
class LogRepository extends EntityRepository
{
    /**
     * @param \Darvin\PaymentBundle\Entity\Log $log Log
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \ReflectionException
     */
    public function saveLog(Log $log): void
    {
        
        if (null === $log->getPayment() && $log->getPayment()->getId()) {
            throw new \LogicException('Can\'t create log, because payment is missing from the database');
        }

        $result = $this->getEntityManager()->getConnection()->executeUpdate('
            INSERT INTO payment_log (payment_id, level, message, created_at, dtype) 
            VALUES (:payment_id, :level, :message, :created_at, :dtype)',
            [
                'payment_id' => $payment->getId(),
                'level'      => $level,
                'message'    => $message,
                'created_at' => new \DateTime(),
                'dtype'      => (new \ReflectionClass($payment))->getShortName(),
            ], [
                'payment_id' => \Doctrine\DBAL\Types\Types::INTEGER,
                'level'      => \Doctrine\DBAL\Types\Types::STRING,
                'message'    => \Doctrine\DBAL\Types\Types::TEXT,
                'created_at' => \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE,
                'dtype'      => \Doctrine\DBAL\Types\Types::STRING,
            ]
        );

        var_dump($result); die;
    }
}
