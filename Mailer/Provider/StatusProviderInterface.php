<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Mailer\Provider;

use Darvin\PaymentBundle\Mailer\Model\PaymentStatus;

/**
 * Payment status provider
 */
interface StatusProviderInterface
{
    /**
     * @param string $name Type name
     *
     * @return \Darvin\PaymentBundle\Mailer\Model\PaymentStatus
     * @throws \InvalidArgumentException
     */
    public function getStatus(string $name): PaymentStatus;

    /**
     * @param string|null $name Type name
     *
     * @return bool
     */
    public function hasStatus(?string $name): bool;

    /**
     * @return \Darvin\PaymentBundle\Mailer\Model\PaymentStatus[]
     */
    public function getAllStatuses(): array;

    /**
     * @return string[]
     */
    public function getAllStatusNames(): array;
}
