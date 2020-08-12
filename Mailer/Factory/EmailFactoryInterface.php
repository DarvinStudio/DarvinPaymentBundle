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
use Darvin\PaymentBundle\Status\Model\PaymentStatus;

/**
 * Payment email factory interface
 */
interface EmailFactoryInterface
{
    /**
     * @param object                                           $order         Order
     * @param \Darvin\PaymentBundle\Status\Model\PaymentStatus $paymentStatus Payment status model
     * @param string                                           $clientEmail   Client Email
     *
     * @return \Darvin\MailerBundle\Model\Email
     *
     * @throws \Darvin\MailerBundle\Factory\Exception\CantCreateEmailException
     */
    public function createPublicEmail(?object $order, PaymentStatus $paymentStatus, string $clientEmail): Email;

    /**
     * @param object                                           $order         Order
     * @param \Darvin\PaymentBundle\Status\Model\PaymentStatus $paymentStatus Payment status model
     *
     * @return \Darvin\MailerBundle\Model\Email
     *
     * @throws \Darvin\MailerBundle\Factory\Exception\CantCreateEmailException
     */
    public function createServiceEmail(?object $order, PaymentStatus $paymentStatus): Email;
}
