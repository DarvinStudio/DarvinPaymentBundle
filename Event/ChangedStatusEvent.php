<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
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
     * @var int
     */
    private $paymentId;

    /**
     * @var int
     */
    private $orderId;

    /**
     * @var string
     */
    private $orderClass;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string|null
     */
    private $clientEmail;

    /**
     * @param PaymentInterface $payment Payment object
     */
    public function __construct(PaymentInterface $payment)
    {
        $this->paymentId = $payment->getId();
        $this->orderId = $payment->getOrderId();
        $this->orderClass = $payment->getOrderEntityClass();
        $this->status = $payment->getStatus();
        $this->clientEmail = $payment->getClientEmail();
    }

    /**
     * @return int
     */
    public function getPaymentId(): int
    {
        return $this->paymentId;
    }

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getOrderClass(): string
    {
        return $this->orderClass;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getClientEmail(): string
    {
        return $this->clientEmail;
    }
}
