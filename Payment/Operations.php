<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Payment;

use Darvin\PaymentBundle\DBAL\Type\PaymentStateType;

/**
 * Workflow transitions
 */
final class Operations
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

    public const OPERATIONS = [
        self::APPROVE   => [PaymentStateType::APPROVAL, PaymentStateType::PENDING],
        self::AUTHORIZE => [PaymentStateType::PENDING, PaymentStateType::AUTHORIZED],
        self::PURCHASE  => [PaymentStateType::PENDING, PaymentStateType::COMPLETED],
        self::EXPIRE    => [PaymentStateType::PENDING, PaymentStateType::EXPIRED],
        self::CANCEL    => [PaymentStateType::PENDING, PaymentStateType::CANCELED],
        self::FAIL      => [PaymentStateType::PENDING, PaymentStateType::FAILED],
        self::REFUND    => [PaymentStateType::COMPLETED, PaymentStateType::REFUNDED],
        self::CAPTURE   => [PaymentStateType::AUTHORIZED, PaymentStateType::COMPLETED],
        self::VOID      => [PaymentStateType::AUTHORIZED, PaymentStateType::CANCELED],
    ];
}
