<?php declare(strict_types=1);
/**
 * @author    Alexander Volodin <mr-stanlik@yandex.ru>
 * @copyright Copyright (c) 2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\EventListener\State\Changed;

use Darvin\MailerBundle\Factory\Exception\CantCreateEmailException;
use Darvin\MailerBundle\Mailer\Exception\MailerException;
use Darvin\MailerBundle\Mailer\MailerInterface;
use Darvin\MailerBundle\Model\Email;
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Mailer\Factory\EmailFactoryInterface;
use Darvin\PaymentBundle\State\Event\ChangedStateEvent;
use Darvin\PaymentBundle\State\Provider\StateProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Payment events subscriber for sending emails about changed state
 */
class SendEmailsSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Darvin\PaymentBundle\Mailer\Factory\EmailFactoryInterface
     */
    private $emailFactory;

    /**
     * @var \Darvin\MailerBundle\Mailer\MailerInterface
     */
    private $mailer;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Darvin\PaymentBundle\State\Provider\StateProviderInterface
     */
    private $stateProvider;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @param \Darvin\PaymentBundle\Mailer\Factory\EmailFactoryInterface  $emailFactory  Payment email factory
     * @param \Darvin\MailerBundle\Mailer\MailerInterface                 $mailer        Mailer
     * @param \Psr\Log\LoggerInterface                                    $logger        Payment logger
     * @param \Darvin\PaymentBundle\State\Provider\StateProviderInterface $stateProvider Provider
     * @param \Symfony\Contracts\Translation\TranslatorInterface          $translator    Translator
     */
    public function __construct(
        EmailFactoryInterface $emailFactory,
        MailerInterface $mailer,
        LoggerInterface $logger,
        StateProviderInterface $stateProvider,
        TranslatorInterface $translator
    ) {
        $this->emailFactory = $emailFactory;
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->stateProvider = $stateProvider;
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ChangedStateEvent::class => 'sendEmails',
        ];
    }

    /**
     * @param \Darvin\PaymentBundle\State\Event\ChangedStateEvent $event Event
     */
    public function sendEmails(ChangedStateEvent $event): void
    {
        $payment = $event->getPayment();

        $state = $this->stateProvider->getState($payment->getState());

        if ($state->getEmail()->getPublicEmail()->isEnabled()) {
            $publicEmail = null;

            try {
                $publicEmail = $this->emailFactory->createPublicEmail($payment, $state);
            } catch (CantCreateEmailException $ex) {
                $this->logger->warning($ex->getMessage(), ['payment' => $payment]);
            }

            if (null !== $publicEmail) {
                $this->mustSend($publicEmail, $payment);
            }
        }

        if ($state->getEmail()->getServiceEmail()->isEnabled()) {
            $serviceEmail = null;

            try {
                $serviceEmail = $this->emailFactory->createServiceEmail($payment, $state);
            } catch (CantCreateEmailException $ex) {
                $this->logger->warning($ex->getMessage(), ['payment' => $payment]);

            }

            if (null !== $serviceEmail) {
                $this->mustSend($serviceEmail, $payment);
            }
        }
    }

    /**
     * @param \Darvin\MailerBundle\Model\Email     $email   Email model
     * @param \Darvin\PaymentBundle\Entity\Payment $payment Payment
     */
    private function mustSend(Email $email, Payment $payment): void
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
