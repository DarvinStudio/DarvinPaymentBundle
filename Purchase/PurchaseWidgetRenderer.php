<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Purchase;

use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Repository\PaymentRepository;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\MappingException;
use Twig\Environment;

/**
 * Purchase widget renderer
 */
class PurchaseWidgetRenderer implements PurchaseWidgetRendererInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em   Entity manager
     * @param \Twig\Environment                    $twig Twig
     */
    public function __construct(EntityManagerInterface $em, Environment $twig)
    {
        $this->em = $em;
        $this->twig = $twig;
    }

    /**
     * {@inheritDoc}
     */
    public function renderPurchaseWidget($order, ?string $orderClass = null): string
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

        $payments = $this->getPaymentRepository()->getForOrder($orderClass, $orderId);

        return $this->twig->render('@DarvinPayment/payment/purchase_widget.html.twig', [
            'payments' => $payments,
        ]);
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

    /**
     * @return \Darvin\PaymentBundle\Repository\PaymentRepository
     */
    private function getPaymentRepository(): PaymentRepository
    {
        return $this->em->getRepository(Payment::class);
    }
}
