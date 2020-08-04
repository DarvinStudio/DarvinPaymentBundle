<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2016-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Event;

use Darvin\PaymentBundle\Entity\PaymentInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Order event
 */
class ChangedStatusEvent extends Event
{
    /**
     * @var \Darvin\PaymentBundle\Entity\PaymentInterface
     */
    private $payment;

    /**
     * @param \Darvin\PaymentBundle\Entity\PaymentInterface $payment Payment
     */
    public function __construct(PaymentInterface $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return \Darvin\PaymentBundle\Entity\PaymentInterface
     */
    public function getPayment(): PaymentInterface
    {
        return $this->payment;
    }
}
