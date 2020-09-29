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
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\State\Model\State;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Payment email factory
 */
class PaymentEmailFactory implements PaymentEmailFactoryInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Darvin\MailerBundle\Factory\TemplateEmailFactoryInterface
     */
    private $genericFactory;

    /**
     * @var \Darvin\PaymentBundle\Config\PaymentConfigInterface
     */
    private $paymentConfig;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface                       $em             Entity manager
     * @param \Darvin\MailerBundle\Factory\TemplateEmailFactoryInterface $genericFactory Generic template email factory
     * @param \Darvin\PaymentBundle\Config\PaymentConfigInterface        $paymentConfig  Payment configuration
     * @param \Symfony\Contracts\Translation\TranslatorInterface         $translator     Translator
     */
    public function __construct(
        EntityManagerInterface $em,
        TemplateEmailFactoryInterface $genericFactory,
        PaymentConfigInterface $paymentConfig,
        TranslatorInterface $translator
    ) {
        $this->em = $em;
        $this->genericFactory = $genericFactory;
        $this->paymentConfig = $paymentConfig;
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function createPublicEmail(Payment $payment, State $state): Email
    {
        if (null === $payment->getClient()->getEmail()) {
            throw new CantCreateEmailException($this->translator->trans('error.missing_public_email', [], 'payment_event'));
        }

        $emailConfig = $state->getPublicEmail();

        return $this->genericFactory->createEmail(
            EmailType::PUBLIC,
            $payment->getClient()->getEmail(),
            $emailConfig->getSubject(),
            $emailConfig->getTemplate(),
            [
                'payment' => $payment,
                'content' => $emailConfig->getContent(),
            ],
            [
                '%orderNumber%' => $payment->getOrder()->getNumber()
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function createServiceEmail(Payment $payment, State $state): Email
    {
        $serviceEmails = $this->paymentConfig->getEmailsByStateName($state->getName());

        if (empty($serviceEmails)) {
            throw new CantCreateEmailException($this->translator->trans('error.missing_service_email', [], 'payment_event'));
        }

        $emailConfig = $state->getServiceEmail();

        return $this->genericFactory->createEmail(
            EmailType::SERVICE,
            $serviceEmails,
            $emailConfig->getSubject(),
            $emailConfig->getTemplate(),
            [
                'payment' => $payment,
                'content' => $emailConfig->getContent(),
                'order'   => $this->getOrder($payment),
            ],
            [
                '%orderNumber%' => $payment->getOrder()->getNumber(),
            ]
        );
    }

    /**
     * @param \Darvin\PaymentBundle\Entity\Payment $payment Payment
     *
     * @return object|null
     */
    private function getOrder(Payment $payment): ?object
    {
        return $this->em->find($payment->getOrder()->getClass(), $payment->getOrder()->getId());
    }
}
