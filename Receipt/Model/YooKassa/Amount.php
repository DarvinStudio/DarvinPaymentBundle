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
 * Amount
 */
class Amount extends AbstractModel
{
    /**
     * Сумма в выбранной валюте.
     *
     * Выражается в виде строки и пишется через точку, например 10.00. Количество знаков после точки зависит от выбранной валюты.
     *
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Regex("/^\d+\.\d+$/")
     */
    protected $value;

    /**
     * Код валюты в формате ISO-4217 (https://www.iso.org/iso-4217-currency-codes.html).
     *
     * Должен соответствовать валюте субаккаунта (recipient.gateway_id), если вы разделяете потоки платежей, и валюте
     * аккаунта (shopId в личном кабинете), если не разделяете.
     *
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Regex("/^[A-Z]{3}$/")
     */
    protected $currency;

    /**
     * @param string $value
     * @param string $currency
     */
    public function __construct(string $value, string $currency)
    {
        $this->value = $value;
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return Amount
     */
    public function setValue(string $value): Amount
    {
        $this->value = $value;

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
     * @return Amount
     */
    public function setCurrency(string $currency): Amount
    {
        $this->currency = $currency;

        return $this;
    }
}
