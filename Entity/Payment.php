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

use Darvin\PaymentBundle\DBAL\Type\PaymentStateType;
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
     * @var int
     *
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank
     */
    protected $orderId;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank
     */
    protected $orderEntityClass;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    protected $transactionReference;

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
     * @ORM\Column
     */
    protected $currencyCode;

    /**
     * @var int|null
     *
     * @ORM\Column(nullable=true)
     */
    protected $clientId;

    /**
     * @var string|null
     *
     * @ORM\Column(length=50, nullable=true)
     *
     * @Assert\Email
     * @Assert\Length(max=50)
     */
    protected $clientEmail;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @var string
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
     * Payment constructor.
     */
    public function __construct()
    {
        $this->state = PaymentStateType::APPROVAL;
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
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     *
     * @return self
     */
    public function setOrderId(int $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderEntityClass(): string
    {
        return $this->orderEntityClass;
    }

    /**
     * @param string $orderEntityClass
     *
     * @return self
     */
    public function setOrderEntityClass(string $orderEntityClass): self
    {
        $this->orderEntityClass = $orderEntityClass;

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
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * @param string $currencyCode
     *
     * @return self
     */
    public function setCurrencyCode(string $currencyCode): self
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClientId(): ?int
    {
        return $this->clientId;
    }

    /**
     * @param string|int|null $clientId
     *
     * @return self
     */
    public function setClientId($clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClientEmail(): ?string
    {
        return $this->clientEmail;
    }

    /**
     * @param string|null $clientEmail
     *
     * @return self
     */
    public function setClientEmail(?string $clientEmail): self
    {
        $this->clientEmail = $clientEmail;

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
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getStateName(): string
    {
        return PaymentStateType::getReadableValue($this->state);
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
     * @return $this
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
     * @return $this
     */
    public function setGatewayName(?string $gatewayName): self
    {
        $this->gatewayName = $gatewayName;

        return $this;
    }
}
