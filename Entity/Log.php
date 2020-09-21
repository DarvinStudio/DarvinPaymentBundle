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
 * @ORM\Table(name="payment_log")
 * @ORM\Entity(repositoryClass="Darvin\PaymentBundle\Repository\LogRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
class Log
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int|null
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(length=10)
     */
    protected $level;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $message;

    /**
     * @var \Darvin\PaymentBundle\Entity\Payment
     *
     * @ORM\ManyToOne(targetEntity="Darvin\PaymentBundle\Entity\Payment", inversedBy="logs")
     */
    protected $payment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * Log constructor.
     *
     * @param \Darvin\PaymentBundle\Entity\Payment $payment
     * @param string                               $level
     * @param string|null                          $message
     */
    public function __construct(Payment $payment, string $level, ?string $message)
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
        return $this->level ?? '';
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * @return \Darvin\PaymentBundle\Entity\Payment
     */
    public function getPayment(): Payment
    {
        return $this->payment;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
