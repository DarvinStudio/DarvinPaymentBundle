<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 08.07.2018
 * Time: 2:44
 */

namespace Darvin\PaymentBundle\Repository;

use Darvin\PaymentBundle\Entity\PaymentInterface;
use Doctrine\ORM\EntityRepository;

class PaymentRepository extends EntityRepository
{
    /**
     * @param string $token
     *
     * @return PaymentInterface|null
     */
    public function findByActionToken(string $token): ?PaymentInterface
    {
        return $this->findOneBy(['actionToken' => $token]);
    }
}
