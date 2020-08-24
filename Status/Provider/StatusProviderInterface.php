<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Status\Provider;

use Darvin\PaymentBundle\Status\Model\PaymentStatus;

/**
 * Payment status provider interface
 */
interface StatusProviderInterface
{
    /**
     * @param string $name Type name
     *
     * @return \Darvin\PaymentBundle\Status\Model\PaymentStatus
     *
     * @throws \Darvin\PaymentBundle\Status\Exception\UnknownStatusException
     */
    public function getStatus(string $name): PaymentStatus;

    /**
     * @param string|null $name Type name
     *
     * @return bool
     */
    public function hasStatus(?string $name): bool;

    /**
     * @return \Darvin\PaymentBundle\Status\Model\PaymentStatus[]
     */
    public function getAllStatuses(): array;

    /**
     * @return string[]
     */
    public function getAllStatusNames(): array;
}
