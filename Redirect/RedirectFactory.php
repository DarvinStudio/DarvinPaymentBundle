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
use Omnipay\Common\Message\ResponseInterface;

/**
 * Redirect factory
 */
class RedirectFactory implements RedirectFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createRedirect(ResponseInterface $response, int $sessionTimeout): Redirect
    {
        if (!$response instanceof RedirectResponseInterface) {
            throw new \LogicException('Response not implemented RedirectResponseInterface');
        }

        return new Redirect(
            $response->getRedirectUrl(),
            $response->getRedirectMethod(),
            $response->getRedirectData(),
            new \DateTime(sprintf('+%s sec', $sessionTimeout))
        );
    }
}
