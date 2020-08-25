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
 * Payment state types
 */
final class PaymentStateType extends AbstractEnumType
{
    public const NEW       = 'new';
    public const PENDING   = 'pending';
    public const COMPLETED = 'completed';
    public const CANCELED  = 'canceled';
    public const FAILED    = 'failed';
    public const REFUND    = 'refund';

    protected static $choices = [
        self::NEW       => 'payment.state.'.self::NEW,
        self::PENDING   => 'payment.state.'.self::PENDING,
        self::COMPLETED => 'payment.state.'.self::COMPLETED,
        self::CANCELED  => 'payment.state.'.self::CANCELED,
        self::FAILED    => 'payment.state.'.self::FAILED,
        self::REFUND    => 'payment.state.'.self::REFUND,
    ];
}
