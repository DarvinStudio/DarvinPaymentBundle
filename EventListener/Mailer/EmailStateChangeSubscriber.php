<?php declare(strict_types=1);
/**
 * @author    Alexander Volodin <mr-stanlik@yandex.ru>
 * @copyright Copyright (c) 2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\EventListener\Mailer;

use Darvin\MailerBundle\Factory\Exception\CantCreateEmailException;
use Darvin\MailerBundle\Mailer\Exception\MailerException;
use Darvin\MailerBundle\Mailer\MailerInterface;
use Darvin\MailerBundle\Model\Email;
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Event\State\ChangedEvent;
use Darvin\PaymentBundle\Mailer\Factory\PaymentEmailFactoryInterface;
use Darvin\PaymentBundle\State\Provider\StateProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Payment events subscriber for sending emails about changed state
 */
class EmailStateChangeSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Darvin\PaymentBundle\Mailer\Factory\PaymentEmailFactoryInterface
     */
    private $emailFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Darvin\MailerBundle\Mailer\MailerInterface
     */
    private $mailer;

    /**
     * @var \Darvin\PaymentBundle\State\Provider\StateProviderInterface
     */
    private $paymentStateProvider;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @param \Darvin\PaymentBundle\Mailer\Factory\PaymentEmailFactoryInterface $emailFactory         Payment email factory
     * @param \Psr\Log\LoggerInterface                                          $logger               Logger
     * @param \Darvin\MailerBundle\Mailer\MailerInterface                       $mailer               Mailer
     * @param \Darvin\PaymentBundle\State\Provider\StateProviderInterface       $paymentStateProvider Payment state provider
     * @param \Symfony\Contracts\Translation\TranslatorInterface                $translator           Translator
     */
    public function __construct(
        PaymentEmailFactoryInterface $emailFactory,
        LoggerInterface $logger,
        MailerInterface $mailer,
        StateProviderInterface $paymentStateProvider,
        TranslatorInterface $translator
    ) {
        $this->emailFactory = $emailFactory;
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->paymentStateProvider = $paymentStateProvider;
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ChangedEvent::class => 'sendEmails',
        ];
    }

    /**
     * @param \Darvin\PaymentBundle\Event\State\ChangedEvent $event State changed event
     */
    public function sendEmails(ChangedEvent $event): void
    {
        $payment = $event->getPayment();

        $state = $this->paymentStateProvider->getState($payment->getState());

        if ($state->getPublicEmail()->isEnabled()) {
            $publicEmail = null;

            try {
                $publicEmail = $this->emailFactory->createPublicEmail($payment, $state);
            } catch (CantCreateEmailException $ex) {
                $this->logger->warning($ex->getMessage(), ['payment' => $payment]);
            }
            if (null !== $publicEmail) {
                $this->send($publicEmail, $payment);
            }
        }
        if ($state->getServiceEmail()->isEnabled()) {
            $serviceEmail = null;

            try {
                $serviceEmail = $this->emailFactory->createServiceEmail($payment, $state);
            } catch (CantCreateEmailException $ex) {
                $this->logger->warning($ex->getMessage(), ['payment' => $payment]);
            }
            if (null !== $serviceEmail) {
                $this->send($serviceEmail, $payment);
            }
        }
    }

    /**
     * @param \Darvin\MailerBundle\Model\Email     $email   Email
     * @param \Darvin\PaymentBundle\Entity\Payment $payment Payment
     */
    private function send(Email $email, Payment $payment): void
    {
        try {
            $this->mailer->mustSend($email);
        } catch (MailerException $ex) {
            $errorMessage = $this->translator->trans('error.cant_send_email', [
                '%message%' => $ex->getMessage(),
            ], 'payment_event');

            $this->logger->warning($errorMessage, ['payment' => $payment]);
        }
    }
}
