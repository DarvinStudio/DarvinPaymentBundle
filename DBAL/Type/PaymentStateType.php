<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\DBAL\Type;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * Payment state enum
 */
final class PaymentStateType extends AbstractEnumType
{
    public const APPROVAL   = 'approval';
    public const AUTHORIZED = 'authorized';
    public const COMPLETED  = 'completed';
    public const CANCELED   = 'canceled';
    public const EXPIRED    = 'expired';
    public const FAILED     = 'failed';
    public const REFUNDED   = 'refunded';
    public const PENDING    = 'pending';

    /**
     * {@inheritDoc}
     */
    protected static $choices = [
        self::APPROVAL   => 'payment.state.approval',
        self::AUTHORIZED => 'payment.state.authorized',
        self::COMPLETED  => 'payment.state.completed',
        self::CANCELED   => 'payment.state.canceled',
        self::EXPIRED    => 'payment.state.expired',
        self::FAILED     => 'payment.state.failed',
        self::REFUNDED   => 'payment.state.refunded',
        self::PENDING    => 'payment.state.pending',
    ];
}
