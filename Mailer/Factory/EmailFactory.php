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

use Darvin\MailerBundle\Factory\Exception\CantCreateEmailException;
use Darvin\MailerBundle\Factory\TemplateEmailFactoryInterface;
use Darvin\MailerBundle\Model\Email;
use Darvin\MailerBundle\Model\EmailType;
use Darvin\PaymentBundle\Config\PaymentConfigInterface;
use Darvin\PaymentBundle\Entity\PaymentInterface;
use Darvin\PaymentBundle\Status\Model\PaymentStatus;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Payment email factory
 */
class EmailFactory implements EmailFactoryInterface
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
     * @inheritDoc
     */
    public function createPublicEmail(PaymentInterface $payment, PaymentStatus $paymentStatus): Email
    {
        if (null === $payment->getClientEmail()) {
            throw new CantCreateEmailException('Client email is not filled');
        }

        return $this->genericFactory->createEmail(
            EmailType::PUBLIC,
            $payment->getClientEmail(),
            $this->translator->trans(sprintf('email.payment.public.%s.subject', $payment->getStatus()), [], 'messages'),
            $paymentStatus->getEmail()->getPublicEmail()->getTemplate(),
            [
                'payment'  => $payment,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function createServiceEmail(PaymentInterface $payment, PaymentStatus $paymentStatus): Email
    {
        $serviceEmails = $this->paymentConfig->getEmailsByStatusName($paymentStatus->getName());

        if (empty($serviceEmails)) {
            throw new CantCreateEmailException(sprintf('Service email for status "%s" is not specified', $paymentStatus->getName()));
        }

        return $this->genericFactory->createEmail(
            EmailType::SERVICE,
            $serviceEmails,
            $this->translator->trans(sprintf('email.payment.service.%s.subject', $payment->getStatus()), [], 'messages'),
            $paymentStatus->getEmail()->getPublicEmail()->getTemplate(),
            [
                'payment'  => $payment,
            ]
        );
    }
}
