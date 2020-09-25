<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Twig\Extension;

use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Purchase Twig extension
 */
class PurchaseExtension extends AbstractExtension
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em Entity manager
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('payment_purchase_widget', [$this, 'renderPurchaseWidget'], [
                'needs_environment' => true,
                'is_safe'           => ['html'],
            ]),
        ];
    }

    /**
     * @param \Twig\Environment $twig       Twig
     * @param mixed             $order      Order object
     * @param string|null       $orderClass Order entity class
     *
     * @return string
     * @throws \LogicException
     */
    public function renderPurchaseWidget(Environment $twig, $order, ?string $orderClass = null): string
    {
        if (is_scalar($order)) {
            $orderId = (string)$order;
        } elseif (is_object($order) && method_exists($order, 'getId') && null !== $order->getId()) {
            $orderId = (string)$order->getId();
        } else {
            throw new \LogicException('Missing order id');
        }

        if (null === $orderClass && is_object($order)) {
            $orderClass = get_class($order);
        } else {
            throw new \LogicException('Missing order entity class');
        }

        $payments = $this->getPaymentRepository()->getForOrder($orderId, $orderClass);

        return $twig->render('@DarvinPayment/payment/purchase_widget.html.twig', [
            'payments' => $payments,
        ]);
    }

    /**
     * @return \Darvin\PaymentBundle\Repository\PaymentRepository
     */
    private function getPaymentRepository(): PaymentRepository
    {
        return $this->em->getRepository(Payment::class);
    }
}
