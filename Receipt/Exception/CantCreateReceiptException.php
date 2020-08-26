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
     * @param Payment $payment
     */
    public function __construct(Payment $payment)
    {
        parent::__construct(sprintf(
            'Can\'t create receipt for order №%s',
            $payment->getOrderId()
        ));
    }
}
