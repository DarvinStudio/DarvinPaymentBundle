<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Config;

/**
 * Payment configuration
 */
interface PaymentConfigInterface
{
    /**
     * @param string $status Payment status
     *
     * @return string[]
     */
    public function getNotificationEmailsByStatus(string $status): array;
}
