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
 * @ORM\Table(name="payment_redirect")
 * @ORM\Entity(repositoryClass="Darvin\PaymentBundle\Repository\RedirectRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
class Redirect
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
     * @var \Darvin\PaymentBundle\Entity\Redirect
     *
     * @ORM\OneToOne(targetEntity="Darvin\PaymentBundle\Entity\Payment", inversedBy="redirect")
     */
    protected $payment;

    /**
     * @var string|null
     *
     * @ORM\Column(length=2048, nullable=true)
     */
    protected $url;

    /**
     * @var string|null
     *
     * @ORM\Column(length=10, nullable=true)
     */
    protected $method;

    /**
     * @var array|null
     *
     * @ORM\Column(type="json", nullable=true)
     */
    protected $data;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $expiryDate;

    /**
     * Redirect constructor.
     *
     * @param string|null    $url        Url
     * @param string|null    $method     Method
     * @param array|null     $data       Data
     * @param \DateTime|null $expiryDate Expire Date
     */
    public function __construct(?string $url, ?string $method, ?array $data, ?\DateTime $expiryDate)
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
        return $this->getUrl() ?? 'Redirect data is empty';
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->getUrl() === null;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        if ($this->getExpiryDate() === null) {
            return false;
        }

        return $this->getExpiryDate()->getTimestamp() < (new \DateTime)->getTimestamp();
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
     * @param \Darvin\PaymentBundle\Entity\Payment|null $payment
     *
     * @return self
     */
    public function setPayment(?Payment $payment): self
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
