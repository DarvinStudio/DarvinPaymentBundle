<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Receipt\Exception;

use Darvin\PaymentBundle\Entity\Payment;

/**
 * Exception for case can't create receipt
 */
class CantCreateReceiptException extends \Exception
{
    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment Payment
     * @param string                               $message Message
     */
    public function __construct(Payment $payment, string $message = '')
    {
        parent::__construct(sprintf(
            'Can\'t create receipt for order #%s: "%s".',
            null !== $payment->getOrder() ? $payment->getOrder()->getNumber() : '',
            $message
        ));
    }
}
