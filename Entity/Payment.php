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

use Darvin\PaymentBundle\DBAL\Type\PaymentStatusType;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table
 * @ORM\Entity(repositoryClass="Darvin\PaymentBundle\Repository\PaymentRepository")
 *
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
class Payment implements PaymentInterface
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int $id
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
     * @ORM\Column(type="PaymentStatusType", nullable=false)
     * @DoctrineAssert\Enum(entity="Darvin\PaymentBundle\DBAL\Type\PaymentStatusType")
     */
    protected $status;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    protected $actionToken;

    /**
     * Payment constructor.
     */
    public function __construct()
    {
        $this->status = PaymentStatusType::NEW;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->status;
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getOrderId(): ?int
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
     * @inheritDoc
     */
    public function getOrderEntityClass(): ?string
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param $status
     *
     * @return self
     */
    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPaid(): bool
    {
        return PaymentStatusType::PAID === $this->status;
    }

    /**
     * @inheritDoc
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
