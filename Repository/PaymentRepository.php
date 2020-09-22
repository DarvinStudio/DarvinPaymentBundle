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
 * Payment Repository
 */
class PaymentRepository extends EntityRepository
{
    /**
     * @param string $orderId    Order ID
     * @param string $orderClass Order class
     * @param string $state      State
     *
     * @return array
     */
    public function getForOrder(string $orderId, string $orderClass, string $state = PaymentStateType::PENDING): array
    {
        return $this->findBy([
            'order.id'    => $orderId,
            'order.class' => $orderClass,
            'state'       => $state,
        ]);
    }
}
