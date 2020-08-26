<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Payment;

use Darvin\PaymentBundle\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Payment manager
 */
class ProxyPaymentFactory implements PaymentFactoryInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var \Darvin\Utils\ORM\EntityResolverInterface
     */
    protected $eventDispatcher;

    /**
     * @var \Darvin\PaymentBundle\Payment\PaymentFactoryInterface
     */
    protected $paymentFactory;

    /**
     * @param EntityManagerInterface   $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param PaymentFactoryInterface  $paymentFactory
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        PaymentFactoryInterface  $paymentFactory
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->paymentFactory = $paymentFactory;
    }

    /**
     * @inheritDoc
     */
    public function createPayment(
        int $orderId,
        string $orderEntityClass,
        string $amount,
        string $currencyCode
    ): Payment {
        $this->paymentFactory->createPayment($orderId, $orderEntityClass, $amount, $currencyCode);
    }
}
