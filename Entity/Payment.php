<?php
/**
 * Created by PhpStorm.
 * User: levsemin
 * Date: 04.07.2018
 * Time: 11:07
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
     * @var integer $id
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
     * @ORM\Column()
     */
    protected $orderEntityClass;

    /**
     * @var string|null
     * @ORM\Column(nullable=true)
     */
    protected $transactionRef;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $amount;

    /**
     * @var string
     *
     * @ORM\Column()
     */
    protected $currencyCode;

    /**
     * @var string|int|null
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return string
     */
    public function getOrderEntityClass()
    {
        return $this->orderEntityClass;
    }

    /**
     * @return null|string
     */
    public function getTransactionRef()
    {
        return $this->transactionRef;
    }

    /**
     * @param null|string $transactionRef
     */
    public function setTransactionRef($transactionRef)
    {
        $this->transactionRef = $transactionRef;
    }

    /**
     * @param string $orderEntityClass
     */
    public function setOrderEntityClass($orderEntityClass)
    {
        $this->orderEntityClass = $orderEntityClass;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @param string $currencyCode
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * @return int|null|string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param int|null|string $clientId
     */
    public function setClientId($clientId = null)
    {
        $this->clientId = $clientId;
    }

    /**
     * @return null|string
     */
    public function getClientEmail()
    {
        return $this->clientEmail;
    }

    /**
     * @param null|string $clientEmail
     */
    public function setClientEmail($clientEmail = null)
    {
        $this->clientEmail = $clientEmail;
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     */
    public function setDescription($description = null)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function isPaid()
    {
        return $this->getStatus() == PaymentStatusType::PAID;
    }

    /**
     * @return null|string
     */
    public function getActionToken()
    {
        return $this->actionToken;
    }

    /**
     * @param null|string $actionToken
     */
    public function setActionToken($actionToken)
    {
        $this->actionToken = $actionToken;
    }
}