<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\State\Model\Email;

/**
 * Model for order type "Public email"
 */
class PublicEmail
{
    use EmailTrait;

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return sprintf('payment.public.%s.subject', $this->name);
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return sprintf('payment.public.%s.content', $this->name);
    }
}
