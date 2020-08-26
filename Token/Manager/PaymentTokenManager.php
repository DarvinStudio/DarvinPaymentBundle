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

use Darvin\PaymentBundle\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Payment token manager
 */
class PaymentTokenManager implements PaymentTokenManagerInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function attach(Payment $payment): void
    {
        $token = md5(sprintf("%d:%d", $payment->getId(), time()));
        $payment->setActionToken($token);

        $this->entityManager->getRepository(Payment::class)->updateState($payment, $token);
    }

    /**
     * @inheritDoc
     */
    public function invalidate(Payment $payment): void
    {
        $payment->setActionToken(null);

        $this->entityManager->getRepository(Payment::class)->updateActionToken($payment, null);
    }
}
