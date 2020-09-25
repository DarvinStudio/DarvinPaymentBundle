<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\State\Provider;

use Darvin\PaymentBundle\State\Model\State;

/**
 * Payment state provider
 */
interface StateProviderInterface
{
    /**
     * @param string $name State name
     *
     * @return \Darvin\PaymentBundle\State\Model\State
     * @throws \InvalidArgumentException
     */
    public function getState(string $name): State;

    /**
     * @param string|null $name State name
     *
     * @return bool
     */
    public function hasState(?string $name): bool;

    /**
     * @return string[]
     */
    public function getAllStateNames(): array;

    /**
     * @return \Darvin\PaymentBundle\State\Model\State[]
     */
    public function getAllStates(): array;
}
