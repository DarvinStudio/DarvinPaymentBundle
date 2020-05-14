<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 06.07.2018
 * Time: 17:10
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
