<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Redirect;

use Darvin\PaymentBundle\Entity\Redirect;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Redirect manager interface
 */
interface RedirectFactoryInterface
{
    /**
     * @param \Omnipay\Common\Message\RedirectResponseInterface $response       Response
     * @param int                                               $sessionTimeout Session Timeout
     *
     * @return \Darvin\PaymentBundle\Entity\Redirect
     */
    public function createRedirect(RedirectResponseInterface $response, int $sessionTimeout): Redirect;
}
