<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Workflow;

use Darvin\PaymentBundle\DBAL\Type\PaymentStateType;

/**
 * Workflow transitions
 */
class Transitions
{
    public const AUTHORIZE = 'authorize';
    public const APPROVE   = 'approve';
    public const CAPTURE   = 'capture';
    public const EXPIRE    = 'expire';
    public const VOID      = 'void';
    public const PURCHASE  = 'purchase';
    public const CANCEL    = 'cancel';
    public const REFUND    = 'refund';
    public const FAIL      = 'fail';

    public const TRANSITIONS = [
        self::APPROVE => [
            'from' => PaymentStateType::APPROVAL,
            'to'   => PaymentStateType::PENDING,
        ],
        self::AUTHORIZE => [
            'from' => PaymentStateType::PENDING,
            'to'   => PaymentStateType::AUTHORIZED,
        ],
        self::CAPTURE => [
            'from' => PaymentStateType::AUTHORIZED,
            'to'   => PaymentStateType::COMPLETED,
        ],
        self::VOID => [
            'from' => PaymentStateType::AUTHORIZED,
            'to'   => PaymentStateType::CANCELED,
        ],
        self::PURCHASE => [
            'from' => PaymentStateType::PENDING,
            'to'   => PaymentStateType::COMPLETED,
        ],
        self::EXPIRE => [
            'from' => PaymentStateType::PENDING,
            'to'   => PaymentStateType::EXPIRED,
        ],
        self::CANCEL => [
            'from' => PaymentStateType::PENDING,
            'to'   => PaymentStateType::CANCELED,
        ],
        self::REFUND => [
            'from' => PaymentStateType::PENDING,
            'to'   => PaymentStateType::REFUNDED,
        ],
        self::FAIL => [
            'from' => PaymentStateType::PENDING,
            'to'   => PaymentStateType::FAILED,
        ],
    ];
}
