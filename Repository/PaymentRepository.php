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

use Darvin\PaymentBundle\DBAL\Type\PaymentStateType;
use Doctrine\ORM\EntityRepository;

/**
 * Payment entity repository
 */
class PaymentRepository extends EntityRepository
{
    /**
     * @param string $orderClass Order class
     * @param string $orderId    Order ID
     * @param string $state      Payment state
     *
     * @return \Darvin\PaymentBundle\Entity\Payment[]
     */
    public function getForOrder(string $orderClass, string $orderId, string $state = PaymentStateType::PENDING): array
    {
        return $this->findBy([
            'order.id'    => $orderId,
            'order.class' => $orderClass,
            'state'       => $state,
        ]);
    }
}
