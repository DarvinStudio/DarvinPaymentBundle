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
use Darvin\PaymentBundle\State\Model\State;

/**
 * Payment email factory interface
 */
interface EmailFactoryInterface
{
    /**
     * @param \Darvin\PaymentBundle\Entity\Payment    $payment     Payment
     * @param \Darvin\PaymentBundle\State\Model\State $state       Payment state model
     *
     * @return \Darvin\MailerBundle\Model\Email
     *
     * @throws \Darvin\MailerBundle\Factory\Exception\CantCreateEmailException
     */
    public function createPublicEmail(Payment $payment, State $state): Email;

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment    $payment     Payment
     * @param \Darvin\PaymentBundle\State\Model\State $state       Payment state model
     *
     * @return \Darvin\MailerBundle\Model\Email
     *
     * @throws \Darvin\MailerBundle\Factory\Exception\CantCreateEmailException
     */
    public function createServiceEmail(Payment $payment, State $state): Email;
}
