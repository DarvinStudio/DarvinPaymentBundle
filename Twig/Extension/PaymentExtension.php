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
        ];
    }

    /**
     * @param Environment $twig             Twig
     * @param object|null $order            Order object
     * @param int|null    $orderId          Order id
     * @param string|null $orderEntityClass Order entity class
     *
     * @return string
     */
    public function renderPaymentWidget(Environment $twig, ?object $order, ?int $orderId = null, ?string $orderEntityClass = null): string
    {
        if (null === $orderId && method_exists($order, 'getId') && is_int($order->getId())) {
            $orderId = $order->getId();
        } else {
            throw new \LogicException('Missing order number');
        }

        if (null === $orderEntityClass && null !== $order) {
            $orderEntityClass = get_class($order);
        } else {
            throw new \LogicException('Missing order entity class');
        }

        $urls = $this->urlManager->getApprovalPaymentUrls($orderId, $orderEntityClass);

        return $twig->render('@DarvinPayment/payment/payment_widget.html.twig', [
            'urls' => $urls,
        ]);
    }
}
