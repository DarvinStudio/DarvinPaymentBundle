<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Event\State;

use Darvin\PaymentBundle\Entity\Payment;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * State changed event
 */
class ChangedEvent extends Event
{
    /**
     * @var \Darvin\PaymentBundle\Entity\Payment
     */
    private $payment;

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment Payment
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return \Darvin\PaymentBundle\Entity\Payment
     */
    public function getPayment(): Payment
    {
        return $this->payment;
    }
}
