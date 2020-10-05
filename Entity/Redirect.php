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
 * @ORM\Entity
 * @ORM\Table(name="payment_redirect")
 */
class Redirect
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
     * @var \Darvin\PaymentBundle\Entity\Payment|null
     *
     * @ORM\OneToOne(targetEntity="Darvin\PaymentBundle\Entity\PaymentInterface", inversedBy="redirect")
     */
    private $payment;

    /**
     * @var string|null
     *
     * @ORM\Column(length=2048, nullable=true)
     */
    private $url;

    /**
     * @var string|null
     *
     * @ORM\Column(length=10, nullable=true)
     */
    private $method;

    /**
     * @var array|null
     *
     * @ORM\Column(type="json", nullable=true)
     */
    private $data;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expiryDate;

    /**
     * @param string|null    $url        URL
     * @param string|null    $method     Method
     * @param array|null     $data       Data
     * @param \DateTime|null $expiryDate Expiry date
     */
    public function __construct(?string $url = null, ?string $method = null, ?array $data = null, ?\DateTime $expiryDate = null)
    {
        $this->url = $url;
        $this->method = $method;
        $this->data = $data;
        $this->expiryDate = $expiryDate;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->url ?? 'Redirect data is empty';
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->url === null;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        if ($this->expiryDate === null) {
            return false;
        }

        return $this->expiryDate->getTimestamp() < (new \DateTime)->getTimestamp();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \Darvin\PaymentBundle\Entity\Payment|null
     */
    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment payment
     *
     * @return Redirect
     */
    public function setPayment(Payment $payment): Redirect
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @return \DateTime|null
     */
    public function getExpiryDate(): ?\DateTime
    {
        return $this->expiryDate;
    }
}
