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
use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\Status\Model\PaymentStatus;

/**
 * Payment email factory interface
 */
interface EmailFactoryInterface
{
    /**
     * @param \Darvin\PaymentBundle\Entity\PaymentInterface    $payment       Payment
     * @param \Darvin\PaymentBundle\Status\Model\PaymentStatus $paymentStatus Payment status model
     *
     * @return \Darvin\MailerBundle\Model\Email
     *
     * @throws \Darvin\MailerBundle\Factory\Exception\CantCreateEmailException
     */
    public function createPublicEmail(PaymentInterface $payment, PaymentStatus $paymentStatus): Email;

    /**
     * @param \Darvin\PaymentBundle\Entity\PaymentInterface    $payment       Payment
     * @param \Darvin\PaymentBundle\Status\Model\PaymentStatus $paymentStatus Payment status model
     *
     * @return \Darvin\MailerBundle\Model\Email
     *
     * @throws \Darvin\MailerBundle\Factory\Exception\CantCreateEmailException
     */
    public function createServiceEmail(PaymentInterface $payment, PaymentStatus $paymentStatus): Email;
}
