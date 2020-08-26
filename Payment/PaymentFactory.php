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
use Darvin\Utils\ORM\EntityResolverInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Payment factory
 */
class PaymentFactory implements PaymentFactoryInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var \Darvin\Utils\ORM\EntityResolverInterface
     */
    protected $entityResolver;

    /**
     * @var string
     */
    protected $defaultCurrency;

    /**
     * PaymentManager constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface      $entityManager   Entity manager
     * @param \Darvin\Utils\ORM\EntityResolverInterface $entityResolver  Entity resolver
     * @param string                                    $defaultCurrency Currency by default
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EntityResolverInterface $entityResolver,
        string $defaultCurrency
    ) {
        $this->entityManager = $entityManager;
        $this->entityResolver = $entityResolver;
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * @inheritdoc
     */
    public function createPayment(
        int $orderId,
        string $orderEntityClass,
        string $amount,
        string $currencyCode
    ): Payment {
        $class = $this->entityResolver->resolve(Payment::class);

        /** @var \Darvin\PaymentBundle\Entity\Payment $payment */
        $payment = new $class(
            $orderId,
            $orderEntityClass,
            $amount,
            $currencyCode ?? $this->defaultCurrency
        );

        return $payment;
    }
}
