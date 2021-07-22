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
 * Item
 */
class Item extends AbstractModel
{
    /**
     * Название товара (не более 128 символов).
     *
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(max=128)
     */
    protected $description;

    /**
     * Количество товара.
     *
     * Максимально возможное значение зависит от модели вашей онлайн-кассы.
     *
     * @var string
     *
     * @Assert\NotBlank
     */
    protected $quantity;

    /**
     * Цена товара.
     *
     * @var \Darvin\PaymentBundle\Receipt\Model\YooKassa\Amount
     *
     * @Assert\NotBlank
     * @Assert\Valid
     */
    protected $amount;

    /**
     * Ставка НДС.
     *
     * Возможные значения — числа от 1 до 6 (https://yookassa.ru/developers/54fz/parameters-values#vat-codes).
     *
     * @var int
     *
     * @Assert\NotBlank
     * @Assert\Range(min=1, max=6)
     */
    protected $vatCode;

    /**
     * Признак предмета расчета (https://yookassa.ru/developers/54fz/parameters-values#payment-subject).
     *
     * @var string|null
     */
    protected $paymentSubject;

    /**
     * Признак способа расчета (https://yookassa.ru/developers/54fz/parameters-values#payment-mode).
     *
     * @var string|null
     */
    protected $paymentMode;

    /**
     * Код товара.
     *
     * Уникальный номер, который присваивается экземпляру товара при маркировке (http://docs.cntd.ru/document/902192509).
     * Формат: число в шестнадцатеричном представлении с пробелами. Максимальная длина — 32 байта.
     * Пример: 00 00 00 01 00 21 FA 41 00 23 05 41 00 00 00 00 00 00 00 00 00 00 00 00 00 00 00 00 12 00 AB 00.
     * Обязательный параметр, если товар нужно маркировать.
     *
     * @var string|null
     */
    protected $productCode;

    /**
     * Код страны происхождения товара по общероссийскому классификатору стран мира (OК (MК (ИСО 3166) 004-97) 025-2001) (http://docs.cntd.ru/document/902192509).
     *
     * Пример: RU. Онлайн-кассы, которые поддерживают этот параметр: Orange Data, Кит Инвест.
     *
     * @var string|null
     *
     * @Assert\Regex("/^[A-Z]{2}$/")
     */
    protected $countryOfOriginCode;

    /**
     * Номер таможенной декларации (от 1 до 32 символов).
     *
     * Онлайн-кассы, которые поддерживают этот параметр: Orange Data, Кит Инвест.
     *
     * @var string|null
     *
     * @Assert\Length(min=1, max=32)
     */
    protected $customsDeclarationNumber;

    /**
     * Сумма акциза товара с учетом копеек. Десятичное число с точностью до 2 символов после точки.
     *
     * Онлайн-кассы, которые поддерживают этот параметр: Orange Data, Кит Инвест.
     *
     * @var string|null
     *
     * @Assert\Regex("/^\d+\.\d{1,2}$/")
     */
    protected $excise;

    /**
     * @param string                                              $description
     * @param string                                              $quantity
     * @param \Darvin\PaymentBundle\Receipt\Model\YooKassa\Amount $amount
     * @param int                                                 $vatCode
     */
    public function __construct(string $description, string $quantity, Amount $amount, int $vatCode)
    {
        $this->description = $description;
        $this->quantity = $quantity;
        $this->amount = $amount;
        $this->vatCode = $vatCode;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Item
     */
    public function setDescription(string $description): Item
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getQuantity(): string
    {
        return $this->quantity;
    }

    /**
     * @param string $quantity
     *
     * @return Item
     */
    public function setQuantity(string $quantity): Item
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return \Darvin\PaymentBundle\Receipt\Model\YooKassa\Amount
     */
    public function getAmount(): Amount
    {
        return $this->amount;
    }

    /**
     * @param \Darvin\PaymentBundle\Receipt\Model\YooKassa\Amount $amount
     *
     * @return Item
     */
    public function setAmount(Amount $amount): Item
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return int
     */
    public function getVatCode(): int
    {
        return $this->vatCode;
    }

    /**
     * @param int $vatCode
     *
     * @return Item
     */
    public function setVatCode(int $vatCode): Item
    {
        $this->vatCode = $vatCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPaymentSubject(): ?string
    {
        return $this->paymentSubject;
    }

    /**
     * @param string|null $paymentSubject
     *
     * @return Item
     */
    public function setPaymentSubject(?string $paymentSubject): Item
    {
        $this->paymentSubject = $paymentSubject;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPaymentMode(): ?string
    {
        return $this->paymentMode;
    }

    /**
     * @param string|null $paymentMode
     *
     * @return Item
     */
    public function setPaymentMode(?string $paymentMode): Item
    {
        $this->paymentMode = $paymentMode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProductCode(): ?string
    {
        return $this->productCode;
    }

    /**
     * @param string|null $productCode
     *
     * @return Item
     */
    public function setProductCode(?string $productCode): Item
    {
        $this->productCode = $productCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountryOfOriginCode(): ?string
    {
        return $this->countryOfOriginCode;
    }

    /**
     * @param string|null $countryOfOriginCode
     *
     * @return Item
     */
    public function setCountryOfOriginCode(?string $countryOfOriginCode): Item
    {
        $this->countryOfOriginCode = $countryOfOriginCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCustomsDeclarationNumber(): ?string
    {
        return $this->customsDeclarationNumber;
    }

    /**
     * @param string|null $customsDeclarationNumber
     *
     * @return Item
     */
    public function setCustomsDeclarationNumber(?string $customsDeclarationNumber): Item
    {
        $this->customsDeclarationNumber = $customsDeclarationNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getExcise(): ?string
    {
        return $this->excise;
    }

    /**
     * @param string|null $excise
     *
     * @return Item
     */
    public function setExcise(?string $excise): Item
    {
        $this->excise = $excise;

        return $this;
    }
}
