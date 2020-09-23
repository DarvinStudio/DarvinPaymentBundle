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
 * Payment state provider interface
 */
interface StateProviderInterface
{
    /**
     * @param string $name Type name
     *
     * @return \Darvin\PaymentBundle\State\Model\State
     */
    public function getState(string $name): State;

    /**
     * @param string|null $name Type name
     *
     * @return bool
     */
    public function hasState(?string $name): bool;

    /**
     * @return \Darvin\PaymentBundle\State\Model\State[]
     */
    public function getAllStates(): array;

    /**
     * @return string[]
     */
    public function getAllStateNames(): array;
}
