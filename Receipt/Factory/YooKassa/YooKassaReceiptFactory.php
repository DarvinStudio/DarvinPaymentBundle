<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2021, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Receipt\Factory\YooKassa;

use Darvin\PaymentBundle\Bridge\BridgeInterface;
use Darvin\PaymentBundle\Bridge\YookassaBridge;
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Receipt\ReceiptFactoryInterface;

/**
 * YooKassa receipt factory
 */
class YooKassaReceiptFactory implements ReceiptFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createReceipt(Payment $payment, BridgeInterface $bridge): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Payment $payment, BridgeInterface $bridge): bool
    {
        return $bridge instanceof YookassaBridge;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'yookassa';
    }
}
