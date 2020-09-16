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
use Darvin\PaymentBundle\Url\PaymentUrlManagerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Payment twig extension
 */
class PaymentExtension extends AbstractExtension
{
    /**
     * @var \Darvin\PaymentBundle\Url\PaymentUrlManagerInterface
     */
    private $urlManager;

    /**
     * @param \Darvin\PaymentBundle\Url\PaymentUrlManagerInterface $urlManager
     */
    public function __construct(PaymentUrlManagerInterface $urlManager)
    {
        $this->urlManager = $urlManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('darvin_payment_widget', [$this, 'renderPaymentWidget'], [
                'needs_environment' => true,
                'is_safe'           => ['html'],
            ]),
            new TwigFunction('darvin_payment_url', [$this, 'getPaymentUrl'], [
                'is_safe'           => ['html'],
            ]),
        ];
    }

    /**
     * @param Environment $twig             Twig
     * @param mixed       $order            Order object
     * @param string|null $orderEntityClass Order entity class
     *
     * @return string
     */
    public function renderPaymentWidget(Environment $twig, $order, ?string $orderEntityClass = null): string
    {
        if (is_scalar($order)) {
            $orderId = $order;
        } elseif (is_object($order) && method_exists($order, 'getId') && null !== $order->getId()) {
            $orderId = $order->getId();
        } else {
            throw new \LogicException('Missing order id');
        }

        if (null === $orderEntityClass && is_object($order)) {
            $orderEntityClass = get_class($order);
        } else {
            throw new \LogicException('Missing order entity class');
        }

        $urls = $this->urlManager->getUrlsForOrder($orderId, $orderEntityClass);

        return $twig->render('@DarvinPayment/payment/payment_widget.html.twig', [
            'urls' => $urls,
        ]);
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment Payment
     *
     * @return array
     */
    public function getPaymentUrl(Payment $payment): array
    {
        return $this->urlManager->getUrlsForPayment($payment);
    }
}
