<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Token\Manager;

use Darvin\PaymentBundle\Entity\PaymentInterface;
use Doctrine\ORM\EntityManager;

/**
 * Class payment token manager
 */
class PaymentTokenManager implements PaymentTokenManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function attach(PaymentInterface $payment): void
    {
        $token = md5(sprintf("%d:%d", $payment->getId(), time()));
        $payment->setActionToken($token);

        $this->entityManager->flush($payment);
    }

    /**
     * @inheritDoc
     */
    public function invalidate(PaymentInterface $payment): void
    {
        $payment->setActionToken(null);
        $this->entityManager->flush($payment);
    }

    /**
     * @inheritDoc
     */
    public function findPayment($token): ?PaymentInterface
    {
        return $this->entityManager->getRepository(PaymentInterface::class)->findByActionToken($token);
    }
}
