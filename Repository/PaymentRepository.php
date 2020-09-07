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

use Darvin\PaymentBundle\Entity\Payment;
use Doctrine\ORM\EntityRepository;

/**
 * Payment Repository
 */
class PaymentRepository extends EntityRepository
{
    /**
     * @param Payment $payment Payment object
     * @param string  $state   State of payment
     *
     * @return void
     */
    public function getPayment(Payment $payment, string $state): void
    {
        $qb = $this->createQueryBuilder('payment');
        $qb
            ->update()
            ->set('payment.state', ':state')
            ->setParameter('state', $state)
            ->where('payment', $payment)
            ->getQuery()
            ->execute();
    }
}
