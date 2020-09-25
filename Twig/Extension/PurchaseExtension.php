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
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\MappingException;
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
     * @throws \InvalidArgumentException
     */
    public function renderPurchaseWidget(Environment $twig, $order, ?string $orderClass = null): string
    {
        $orderId = $this->getOrderId($order);

        if (null === $orderId) {
            throw new \InvalidArgumentException('Unable to retrieve order ID.');
        }
        if (null === $orderClass) {
            $orderClass = $this->getOrderClass($order);

            if (null === $orderClass) {
                throw new \InvalidArgumentException('Unable to retrieve order class.');
            }
        }
        if (!class_exists($orderClass)) {
            throw new \InvalidArgumentException(sprintf('Order class "%s" does not exist.', $orderClass));
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

    /**
     * @param mixed $order Order
     *
     * @return string|null
     */
    private function getOrderId($order): ?string
    {
        if (is_scalar($order)) {
            return (string)$order;
        }
        if (is_object($order)) {
            try {
                $meta = $this->em->getClassMetadata(ClassUtils::getClass($order));
            } catch (MappingException $ex) {
                return null;
            }

            $ids = $meta->getIdentifierValues($order);

            if (empty($ids)) {
                return null;
            }

            $id = reset($ids);

            if (null !== $id) {
                $id = (string)$id;
            }

            return $id;
        }

        return null;
    }

    /**
     * @param mixed $order Order
     *
     * @return string|null
     */
    private function getOrderClass($order): ?string
    {
        if (is_object($order)) {
            return ClassUtils::getClass($order);
        }

        return null;
    }
}
