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

/**
 * YooKassa receipt model abstract implementation
 */
abstract class AbstractModel
{
    /**
     * @return array
     */
    public function getData(): array
    {
        return get_object_vars($this);
    }
}
