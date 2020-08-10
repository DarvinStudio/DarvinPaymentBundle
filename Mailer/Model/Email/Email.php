<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Mailer\Model\Email;

/**
 * Email for payment status
 */
class Email
{
    /**
     * @var \Darvin\PaymentBundle\Mailer\Model\Email\PublicEmail
     */
    private $publicEmail;

    /**
     * @var \Darvin\PaymentBundle\Mailer\Model\Email\ServiceEmail
     */
    private $serviceEmail;

    /**
     * @param \Darvin\PaymentBundle\Mailer\Model\Email\PublicEmail  $publicEmail  Public email
     * @param \Darvin\PaymentBundle\Mailer\Model\Email\ServiceEmail $serviceEmail Service email
     */
    public function __construct(PublicEmail $publicEmail, ServiceEmail $serviceEmail)
    {
        $this->publicEmail = $publicEmail;
        $this->serviceEmail = $serviceEmail;
    }

    /**
     * @return \Darvin\PaymentBundle\Mailer\Model\Email\PublicEmail
     */
    public function getPublicEmail(): PublicEmail
    {
        return $this->publicEmail;
    }

    /**
     * @return \Darvin\PaymentBundle\Mailer\Model\Email\ServiceEmail
     */
    public function getServiceEmail(): ServiceEmail
    {
        return $this->serviceEmail;
    }
}
