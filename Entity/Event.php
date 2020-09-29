<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Darvin\PaymentBundle\Repository\EventRepository")
 * @ORM\Table(name="payment_event")
 */
class Event
{
    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue
     * @ORM\Id
     */
    private $id;

    /**
     * @var \Darvin\PaymentBundle\Entity\Payment
     *
     * @ORM\ManyToOne(targetEntity="Darvin\PaymentBundle\Entity\PaymentInterface", inversedBy="events")
     */
    private $payment;

    /**
     * @var string
     *
     * @ORM\Column(length=10)
     */
    private $level;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment Payment
     * @param string                               $level   Level
     * @param string|null                          $message Message
     */
    public function __construct(Payment $payment, string $level, ?string $message = null)
    {
        $this->payment = $payment;
        $this->level = $level;
        $this->message = $message;

        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->level;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \Darvin\PaymentBundle\Entity\Payment
     */
    public function getPayment(): Payment
    {
        return $this->payment;
    }

    /**
     * @return string
     */
    public function getLevel(): string
    {
        return $this->level;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
