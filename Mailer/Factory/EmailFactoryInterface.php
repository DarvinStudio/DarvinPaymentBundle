<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Mailer\Factory;

use Darvin\MailerBundle\Model\Email;
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\UserBundle\Entity\BaseUser;

/**
 * Payment email factory
 */
interface EmailFactoryInterface
{
    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment Payment
     * @param \Darvin\UserBundle\Entity\BaseUser   $user    User
     *
     * @return \Darvin\MailerBundle\Model\Email
     * @throws \Darvin\MailerBundle\Factory\Exception\CantCreateEmailException
     */
    public function createPaidStatusEmail(Payment $payment, ?BaseUser $user): Email;

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment Payment
     * @param \Darvin\UserBundle\Entity\BaseUser   $user    User
     *
     * @return \Darvin\MailerBundle\Model\Email
     * @throws \Darvin\MailerBundle\Factory\Exception\CantCreateEmailException
     */
    public function createFailedStatusEmail(Payment $payment, ?BaseUser $user): Email;
}
