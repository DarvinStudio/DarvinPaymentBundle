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

/**
 * @ORM\Table
 * @ORM\Entity(repositoryClass="Darvin\PaymentBundle\Repository\PaymentRepository")
 * @ORM\HasLifecycleCallbacks
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
     */
    protected $orderId;

    /**
     * @var string
     *
     * @ORM\Column
     */
    protected $orderEntityClass;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    protected $transactionRef;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", scale=2)
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
     * @ORM\Column(nullable=true)
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
     * @param int    $orderId          Order ID
     * @param string $orderEntityClass Class of order entity
     * @param string $amount           Amount
     * @param string $currencyCode     Currency Code
     */
    public function __construct(
        int $orderId,
        string $orderEntityClass,
        string $amount,
        string $currencyCode
    ) {
        $this->orderId = $orderId;
        $this->orderEntityClass = $orderEntityClass;
        $this->amount = $amount;
        $this->currencyCode = $currencyCode;

        $this->state = PaymentStateType::NEW;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->state;
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
    public function getTransactionRef(): ?string
    {
        return $this->transactionRef;
    }

    /**
     * @param string|null $transactionRef
     *
     * @return self
     */
    public function setTransactionRef(?string $transactionRef): self
    {
        $this->transactionRef = $transactionRef;

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
}
