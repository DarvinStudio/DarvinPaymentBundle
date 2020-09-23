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
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table
 * @ORM\Entity(repositoryClass="Darvin\PaymentBundle\Repository\PaymentRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 *
 * @HasLifecycleCallbacks
 */
class Payment
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
     * @var \Darvin\PaymentBundle\Entity\Redirect|null
     *
     * @ORM\OneToOne(targetEntity="Darvin\PaymentBundle\Entity\Redirect", mappedBy="payment", cascade="remove")
     */
    protected $redirect;

    /**
     * @var \Doctrine\Common\Collections\Collection|\Darvin\PaymentBundle\Entity\Event[]
     *
     * @ORM\OneToMany(targetEntity="Darvin\PaymentBundle\Entity\Event", mappedBy="payment", cascade={"remove"})
     */
    protected $events;

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
     * @ORM\Column(length=36, nullable=true)
     */
    protected $token;

    /**
     * @var string|null
     *
     * @ORM\Column(length=20, nullable=true)
     *
     * @Assert\Length(max=20)
     */
    protected $gateway;

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
        $this->createdAt = new \DateTime();
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getId();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
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
     * @return \Darvin\PaymentBundle\Entity\Redirect|null
     */
    public function getRedirect(): ?Redirect
    {
        return $this->redirect;
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Redirect|null $redirect
     *
     * @return self
     */
    public function setRedirect(?Redirect $redirect): self
    {
        $this->redirect = $redirect;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasRedirect(): bool
    {
        return null !== $this->redirect;
    }

    /**
     * @return \Darvin\PaymentBundle\Entity\Event[]|\Doctrine\Common\Collections\Collection
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Event[]|\Doctrine\Common\Collections\Collection $events events
     *
     * @return self
     */
    public function setEvents(Collection $events): self
    {
        $this->events = $events;

        return $this;
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Event Event
     *
     * @return self
     */
    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
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
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     *
     * @return self
     */
    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGateway(): ?string
    {
        return $this->gateway;
    }

    /**
     * @param string|null $gateway
     *
     * @return self
     */
    public function setGateway(?string $gateway): self
    {
        $this->gateway = $gateway;

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
