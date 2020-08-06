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

class PaymentStatusType extends AbstractEnumType
{
    public const NEW      = 'new';
    public const PENDING  = 'pending';
    public const PAID     = 'paid';
    public const CANCELED = 'canceled';
    public const FAILED   = 'failed';
    public const REFUND   = 'refund';

    protected static $choices = [
        self::NEW      => 'payment.status.'.self::NEW,
        self::PENDING  => 'payment.status.'.self::PENDING,
        self::PAID     => 'payment.status.'.self::PAID,
        self::CANCELED => 'payment.status.'.self::CANCELED,
        self::FAILED   => 'payment.status.'.self::FAILED,
        self::REFUND   => 'payment.status.'.self::REFUND,
    ];
}
