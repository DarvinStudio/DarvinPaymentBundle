<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Url;

use Darvin\PaymentBundle\DBAL\Type\PaymentStateType;
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Payment url factory
 */
class PaymentUrlManager implements PaymentUrlManagerInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface
     */
    private $urlBuilder;

    /**
     * @var array
     */
    private $gatewayNames;

    /**
     * @var bool
     */
    private $preAuthorize;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface                 $em           Entity manager
     * @param \Darvin\PaymentBundle\Url\PaymentUrlBuilderInterface $urlBuilder   Url builder
     * @param bool                                                 $preAuthorize Pre-authorize payment enable
     */
    public function __construct(
        EntityManagerInterface $em,
        PaymentUrlBuilderInterface $urlBuilder,
        bool $preAuthorize
    ) {
        $this->em = $em;
        $this->urlBuilder = $urlBuilder;
        $this->preAuthorize = $preAuthorize;
    }

    /**
     * @param string $name
     */
    public function addGatewayName(string $name): void
    {
        $this->gatewayNames[] = $name;
    }

    /**
     * @inheritdoc
     */
    public function getUrlsForOrder(int $orderId, string $orderEntityClass): array
    {
        $payments = $this->getPaymentRepository()->findBy([
            'order.id' => $orderId,
            'order.class' => $orderEntityClass,
            'state' => PaymentStateType::PENDING,
        ]);

        $urls = [];

        /** @var $payment \Darvin\PaymentBundle\Entity\Payment */
        foreach ($payments as $payment) {
            $urls[$payment->getId()] = $this->getUrlsForPayment($payment);
        }

        return $urls;
    }

    /**
     * @inheritdoc
     */
    public function getUrlsForPayment(Payment $payment): array
    {
        $urls = [];

        foreach ($this->gatewayNames as $gatewayName) {
            $urls[$gatewayName] = $this->preAuthorize
                ? $this->urlBuilder->getAuthorizeUrl($payment, $gatewayName)
                : $this->urlBuilder->getPurchaseUrl($payment, $gatewayName)
            ;
        }

        return $urls;
    }

    /**
     * @return \Darvin\PaymentBundle\Repository\PaymentRepository
     */
    private function getPaymentRepository(): PaymentRepository
    {
        return $this->em->getRepository(Payment::class);
    }
}
