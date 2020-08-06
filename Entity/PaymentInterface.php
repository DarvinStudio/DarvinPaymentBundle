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

interface PaymentInterface
{
    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @return int|null
     */
    public function getOrderId(): ?int;

    /**
     * @return string Request or Order class name
     */
    public function getOrderEntityClass(): ?string;

    /**
     * @return string|null
     */
    public function getTransactionRef(): ?string;

    /**
     * @param string|null $reference
     *
     * @return self
     */
    public function setTransactionRef(?string $reference);

    /**
     * @return string
     */
    public function getAmount(): string;

    /**
     * @return string Currency code in ISO format (USD, RUB, AED...)
     */
    public function getCurrencyCode(): string;

    /**
     * @return string|int|null
     */
    public function getClientId();

    /**
     * @return string|null
     */
    public function getClientEmail(): ?string;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @return string One of PaymentStatusType constant
     */
    public function getStatus(): string;

    /**
     * @param string $status One of PaymentStatusType constant
     *
     * @return self
     */
    public function setStatus(string $status);

    /**
     * @return bool
     */
    public function isPaid(): bool;

    /**
     * @return string|null
     */
    public function getActionToken(): ?string;

    /**
     * @param string|null $token
     *
     * @return self
     */
    public function setActionToken(?string $token);
}
