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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as Doctrine;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Payment
 *
 * @ORM\Entity(repositoryClass="Darvin\PaymentBundle\Repository\PaymentRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 *
 * @Doctrine\UniqueEntity(fields={"token"})
 */
class Payment implements PaymentInterface
{
    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue
     * @ORM\Id
     */
    protected $id;

    /**
     * @var \Darvin\PaymentBundle\Entity\Redirect|null
     *
     * @ORM\OneToOne(targetEntity="Darvin\PaymentBundle\Entity\Redirect", mappedBy="payment", cascade={"remove"})
     */
    protected $redirect;

    /**
     * @var \Doctrine\Common\Collections\Collection|\Darvin\PaymentBundle\Entity\Event[]
     *
     * @ORM\OneToMany(targetEntity="Darvin\PaymentBundle\Entity\Event", mappedBy="payment", cascade={"remove"})
     */
    protected $events;

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
     *
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
     * @ORM\Column(length=36, unique=true)
     *
     * @Assert\Length(max=36)
     * @Assert\NotBlank
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
     * @param \Darvin\PaymentBundle\Entity\PaidOrder $order    Order
     * @param string                                 $amount   Amount
     * @param string                                 $currency Currency code
     */
    public function __construct(PaidOrder $order, string $amount, string $currency)
    {
        $this->order = $order;
        $this->amount = $amount;
        $this->currency = $currency;

        $this->events = new ArrayCollection();
        $this->client = new Client();
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->id;
    }

    /**
     * @return bool
     */
    public function hasRedirect(): bool
    {
        return null !== $this->redirect;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \Darvin\PaymentBundle\Entity\Redirect|null
     */
    public function getRedirect(): ?Redirect
    {
        return $this->redirect;
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Redirect|null $redirect redirect
     *
     * @return Payment
     */
    public function setRedirect(?Redirect $redirect): Payment
    {
        $this->redirect = $redirect;

        return $this;
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
     * @return Payment
     */
    public function setEvents(Collection $events): Payment
    {
        $this->events = $events;

        return $this;
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Event Event
     *
     * @return Payment
     */
    public function addEvent(Event $event): Payment
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
        }

        return $this;
    }

    /**
     * @return \Darvin\PaymentBundle\Entity\PaidOrder
     */
    public function getOrder(): PaidOrder
    {
        return $this->order;
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\PaidOrder $order order
     *
     * @return Payment
     */
    public function setOrder(PaidOrder $order): Payment
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return \Darvin\PaymentBundle\Entity\Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Client $client client
     *
     * @return Payment
     */
    public function setClient(Client $client): Payment
    {
        $this->client = $client;

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
     * @param string $amount amount
     *
     * @return Payment
     */
    public function setAmount(string $amount): Payment
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
     * @param string $currency currency
     *
     * @return Payment
     */
    public function setCurrency(string $currency): Payment
    {
        $this->currency = $currency;

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
     * @param string|null $state state
     *
     * @return Payment
     */
    public function setState(?string $state): Payment
    {
        $this->state = $state;

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
     * @param string|null $transactionReference transactionReference
     *
     * @return Payment
     */
    public function setTransactionReference(?string $transactionReference): Payment
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
     * @param string|null $description description
     *
     * @return Payment
     */
    public function setDescription(?string $description): Payment
    {
        $this->description = $description;

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
     * @param string|null $token token
     *
     * @return Payment
     */
    public function setToken(?string $token): Payment
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
     * @param string|null $gateway gateway
     *
     * @return Payment
     */
    public function setGateway(?string $gateway): Payment
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
     * @return Payment
     */
    public function setCreatedAt(\DateTime $createdAt): Payment
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
