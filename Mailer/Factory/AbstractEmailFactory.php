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

use Darvin\MailerBundle\Factory\TemplateEmailFactoryInterface;
use Darvin\MailerBundle\Model\Email;
use Darvin\PaymentBundle\Config\PaymentConfigInterface;
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\UserBundle\Entity\BaseUser;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Abstract email factory
 */
abstract class AbstractEmailFactory implements EmailFactoryInterface
{
    /**
     * @var \Darvin\MailerBundle\Factory\TemplateEmailFactoryInterface
     */
    protected $genericFactory;

    /**
     * @var \Darvin\PaymentBundle\Config\PaymentConfigInterface
     */
    protected $paymentConfig;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @param \Darvin\MailerBundle\Factory\TemplateEmailFactoryInterface $genericFactory Generic template email factory
     * @param \Darvin\PaymentBundle\Config\PaymentConfigInterface        $paymentConfig  Payment configuration
     * @param \Symfony\Contracts\Translation\TranslatorInterface         $translator     Translator
     */
    public function __construct(TemplateEmailFactoryInterface $genericFactory, PaymentConfigInterface $paymentConfig, TranslatorInterface $translator)
    {
        $this->genericFactory = $genericFactory;
        $this->paymentConfig = $paymentConfig;
        $this->translator = $translator;
    }

    /**
     * @param string                                   $emailType Email type
     * @param mixed                                    $to        To
     * @param string                                   $subject   Subject
     * @param string                                   $template  Template
     * @param \Darvin\PaymentBundle\Entity\Payment     $payment   Payment
     * @param \Darvin\UserBundle\Entity\BaseUser|null  $user      User
     *
     * @return \Darvin\MailerBundle\Model\Email
     * @throws \Darvin\MailerBundle\Factory\Exception\CantCreateEmailException
     */
    protected function createEmail(string $emailType, $to, string $subject, string $template, Payment $payment, ?BaseUser $user = null): Email
    {
        return $this->genericFactory->createEmail(
            $emailType,
            $to,
            $subject,
            $template,
            [
                'payment'  => $payment,
                'user'  => $user,
            ],
            []
        );
    }
}
