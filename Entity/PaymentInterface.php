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

/**
 * Payment interface
 */
interface PaymentInterface
{
    /**
     * @return int
     */
    public function getId(): int;

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
     * @return string
     */
    public function getAmount(): string;

    /**
     * @return string Currency code in ISO format (USD, RUB, AED...)
     */
    public function getCurrencyCode(): string;

    /**
     * @return int|null
     */
    public function getClientId(): ?int;

    /**
     * @return string|null
     */
    public function getClientEmail(): ?string;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @return string One of PaymentStateType constant
     */
    public function getState(): string;

    /**
     * @return bool
     */
    public function isPaid(): bool;

    /**
     * @return string|null
     */
    public function getActionToken(): ?string;
}
