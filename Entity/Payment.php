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

use Doctrine\Common\Collections\Collection;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table
 * @ORM\Entity(repositoryClass="Darvin\PaymentBundle\Repository\PaymentRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
class Payment
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    protected $id;

    /**
     * @var \Darvin\PaymentBundle\Entity\PaidOrder
     *
     * @ORM\Embedded(class="Darvin\PaymentBundle\Entity\PaidOrder")
     *
     * @Assert\Valid
     */
    protected $order;

    /**
     * @var \Darvin\PaymentBundle\Entity\Client
     *
     * @ORM\Embedded(class="Darvin\PaymentBundle\Entity\Client")
     *
     * @Assert\Valid
     */
    protected $client;

    /**
     * @var \Darvin\PaymentBundle\Entity\Redirect
     *
     * @ORM\Embedded(class="Darvin\PaymentBundle\Entity\Redirect")
     */
    protected $redirect;

    /**
     * @var \Doctrine\Common\Collections\Collection|\Darvin\PaymentBundle\Entity\Log[]
     *
     * @ORM\OneToMany(targetEntity="Darvin\PaymentBundle\Entity\Log", mappedBy="payment", cascade={"remove"})
     */
    protected $logs;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", scale=2)
     *
     * @Assert\GreaterThanOrEqual(0)
     */
    protected $amount;

    /**
     * @var string
     *
     * @ORM\Column(length=3)
     *
     * @Assert\Length(max=3)
     * @Assert\NotBlank
     */
    protected $currency;

    /**
     * @var string|null
     *
     * @ORM\Column(type="PaymentStateType")
     * @DoctrineAssert\Enum(entity="Darvin\PaymentBundle\DBAL\Type\PaymentStateType")
     */
    protected $state;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    protected $transactionReference;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    protected $actionToken;

    /**
     * @var string|null
     *
     * @ORM\Column(length=20, nullable=true)
     *
     * @Assert\Length(max=20)
     */
    protected $gatewayName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotBlank
     * @Assert\LessThanOrEqual("now")
     */
    protected $createdAt;

    /**
     * Payment constructor.
     *
     * @param \Darvin\PaymentBundle\Entity\PaidOrder $order    Order
     * @param \Darvin\PaymentBundle\Entity\Client    $client   Client
     * @param string                                 $amount   Amount
     * @param string                                 $currency Currency Code
     */
    public function __construct(PaidOrder $order, Client $client, string $amount, string $currency)
    {
        $this->order = $order;
        $this->client = $client;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->redirect = new Redirect();
        $this->createdAt = new \DateTime();
        $this->logs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getId();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \Darvin\PaymentBundle\Entity\PaidOrder
     */
    public function getOrder(): PaidOrder
    {
        return $this->order;
    }

    /**
     * @return \Darvin\PaymentBundle\Entity\Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return \Darvin\PaymentBundle\Entity\Redirect
     */
    public function getRedirect(): Redirect
    {
        return $this->redirect;
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Redirect $redirect
     *
     * @return self
     */
    public function setRedirect(Redirect $redirect): self
    {
        $this->redirect = $redirect;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasRedirect(): bool
    {
        return !$this->redirect->isEmpty();
    }

    /**
     * @return \Darvin\PaymentBundle\Entity\Log[]|\Doctrine\Common\Collections\Collection
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Log[]|\Doctrine\Common\Collections\Collection $logs logs
     *
     * @return self
     */
    public function setLogs(Collection $logs): self
    {
        $this->logs = $logs;

        return $this;
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Log Log
     *
     * @return self
     */
    public function addLog(Log $log): self
    {
        if (!$this->logs->contains($log)) {
            $this->logs->add($log);
            $log->setPayment($this);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     *
     * @return self
     */
    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     *
     * @return self
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTransactionReference(): ?string
    {
        return $this->transactionReference;
    }

    /**
     * @param string|null $transactionReference
     *
     * @return self
     */
    public function setTransactionReference(?string $transactionReference): self
    {
        $this->transactionReference = $transactionReference;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return self
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param $state
     *
     * @return self
     */
    public function setState($state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getActionToken(): ?string
    {
        return $this->actionToken;
    }

    /**
     * @param string|null $actionToken
     *
     * @return self
     */
    public function setActionToken(?string $actionToken): self
    {
        $this->actionToken = $actionToken;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGatewayName(): ?string
    {
        return $this->gatewayName;
    }

    /**
     * @param string|null $gatewayName
     *
     * @return self
     */
    public function setGatewayName(?string $gatewayName): self
    {
        $this->gatewayName = $gatewayName;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt createdAt
     *
     * @return self
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
