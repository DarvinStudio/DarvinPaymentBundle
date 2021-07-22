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
 * Customer
 */
class Customer extends AbstractModel
{
    /**
     * Для юрлица — название организации, для ИП и физического лица — ФИО.
     *
     * Если у физлица отсутствует ИНН, в этом же параметре передаются паспортные данные. Не более 256 символов.
     * Онлайн-кассы, которые поддерживают этот параметр: Orange Data, АТОЛ Онлайн.
     *
     * @var string|null
     *
     * @Assert\Length(max=256)
     */
    protected $fullName;

    /**
     * ИНН пользователя (10 или 12 цифр).
     *
     * @var string|null
     *
     * @Assert\Regex("/^(\d{10}|\d{12})$/")
     */
    protected $inn;

    /**
     * Электронная почта пользователя.
     *
     * @var string|null
     *
     * @Assert\Email
     */
    protected $email;

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
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @param string|null $fullName
     *
     * @return Customer
     */
    public function setFullName(?string $fullName): Customer
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getInn(): ?string
    {
        return $this->inn;
    }

    /**
     * @param string|null $inn
     *
     * @return Customer
     */
    public function setInn(?string $inn): Customer
    {
        $this->inn = $inn;

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
     * @return Customer
     */
    public function setEmail(?string $email): Customer
    {
        $this->email = $email;

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
     * @return Customer
     */
    public function setPhone(?string $phone): Customer
    {
        $this->phone = $phone;

        return $this;
    }
}
