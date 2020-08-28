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
    public const CAPTURE   = 'capture';
    public const VOID      = 'void';
    public const PURCHASE  = 'purchase';
    public const CANCEL    = 'cancel';
    public const REFUND    = 'refund';

    public const TRANSITIONS = [
        self::AUTHORIZE => [
            PaymentStateType::PENDING,
            PaymentStateType::AUTHORIZED,
        ],
        self::CAPTURE => [
            PaymentStateType::AUTHORIZED,
            PaymentStateType::COMPLETED,
        ],
        self::VOID => [
            PaymentStateType::AUTHORIZED,
            PaymentStateType::CANCELED,
        ],
        self::PURCHASE => [
            PaymentStateType::PENDING,
            PaymentStateType::COMPLETED,
        ],
        self::CANCEL => [
            PaymentStateType::PENDING,
            PaymentStateType::CANCELED,
        ],
        self::REFUND => [
            PaymentStateType::PENDING,
            PaymentStateType::REFUNDED,
        ],
    ];
}
