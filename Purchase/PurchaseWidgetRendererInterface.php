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

/**
 * Purchase widget renderer
 */
interface PurchaseWidgetRendererInterface
{
    /**
     * @param mixed       $order      Order
     * @param string|null $orderClass Order class
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function renderPurchaseWidget($order, ?string $orderClass = null): string;
}
