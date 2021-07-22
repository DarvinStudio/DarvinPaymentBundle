<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2021, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Receipt\Model\YooKassa;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Receipt
 */
class Receipt extends AbstractModel
{
    /**
     * Информация о пользователе.
     *
     * Необходимо указать как минимум контактные данные: электронную почту (customer.email) или номер телефона (customer.phone).
     *
     * @var \Darvin\PaymentBundle\Receipt\Model\YooKassa\Customer|null
     *
     * @Assert\Valid
     */
    protected $customer;

    /**
     * Список товаров в заказе (не более 100 товаров).
     *
     * @var \Darvin\PaymentBundle\Receipt\Model\YooKassa\Item[]
     *
     * @Assert\Valid
     * @Assert\Count(min=1, max=100)
     */
    protected $items;

    /**
     * Система налогообложения магазина.
     *
     * Параметр необходим, только если у вас несколько систем налогообложения, в остальных случаях не передается
     * (https://yookassa.ru/developers/54fz/parameters-values#tax-systems).
     *
     * @var int|null
     *
     * @Assert\Range(min=1, max=6)
     */
    protected $taxSystemCode;

    /**
     * Телефон пользователя.
     *
     * Указывается в формате ITU-T E.164, например 79000000000 (https://ru.wikipedia.org/wiki/E.164).
     *
     * @var string|null
     *
     * @Assert\Regex("/^\d{1,15}$/")
     */
    protected $phone;

    /**
     * Электронная почта пользователя.
     *
     * @var string|null
     *
     * @Assert\Email
     */
    protected $email;

    public function __construct()
    {
        $this->items = [];
    }

    /**
     * @return \Darvin\PaymentBundle\Receipt\Model\YooKassa\Customer|null
     */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    /**
     * @param \Darvin\PaymentBundle\Receipt\Model\YooKassa\Customer|null $customer
     *
     * @return Receipt
     */
    public function setCustomer(?Customer $customer): Receipt
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return \Darvin\PaymentBundle\Receipt\Model\YooKassa\Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param \Darvin\PaymentBundle\Receipt\Model\YooKassa\Item[] $items
     *
     * @return Receipt
     */
    public function setItems(array $items): Receipt
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @param \Darvin\PaymentBundle\Receipt\Model\YooKassa\Item $item
     *
     * @return Receipt
     */
    public function addItem(Item $item): Receipt
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTaxSystemCode(): ?int
    {
        return $this->taxSystemCode;
    }

    /**
     * @param int|null $taxSystemCode
     *
     * @return Receipt
     */
    public function setTaxSystemCode(?int $taxSystemCode): Receipt
    {
        $this->taxSystemCode = $taxSystemCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     *
     * @return Receipt
     */
    public function setPhone(?string $phone): Receipt
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     *
     * @return Receipt
     */
    public function setEmail(?string $email): Receipt
    {
        $this->email = $email;

        return $this;
    }
}
