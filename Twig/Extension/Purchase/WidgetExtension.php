<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Twig\Extension\Purchase;

use Darvin\PaymentBundle\Purchase\PurchaseWidgetRendererInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Purchase widget Twig extension
 */
class WidgetExtension extends AbstractExtension
{
    /**
     * @var \Darvin\PaymentBundle\Purchase\PurchaseWidgetRendererInterface
     */
    private $renderer;

    /**
     * @param \Darvin\PaymentBundle\Purchase\PurchaseWidgetRendererInterface $renderer Purchase widget renderer
     */
    public function __construct(PurchaseWidgetRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('payment_purchase_widget', [$this->renderer, 'renderPurchaseWidget'], [
                'is_safe' => ['html'],
            ]),
        ];
    }
}
