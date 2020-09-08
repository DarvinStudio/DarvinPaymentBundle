<?php declare(strict_types=1);
/**
 * @author    Alexander Volodin <mr-stanlik@yandex.ru>
 * @copyright Copyright (c) 2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\EventListener\State;

use Darvin\MailerBundle\Factory\Exception\CantCreateEmailException;
use Darvin\MailerBundle\Mailer\Exception\MailerException;
use Darvin\MailerBundle\Mailer\MailerInterface;
use Darvin\MailerBundle\Model\Email;
use Darvin\PaymentBundle\Entity\Payment;
use Darvin\PaymentBundle\Logger\PaymentLoggerInterface;
use Darvin\PaymentBundle\Mailer\Factory\EmailFactoryInterface;
use Darvin\PaymentBundle\State\Provider\StateProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

/**
 * Class payment events subscriber
 */
class SendChangedEmailsSubscriber implements EventSubscriberInterface
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
     * @var \Darvin\PaymentBundle\Logger\PaymentLoggerInterface
     */
    protected $logger;

    /**
     * @var \Darvin\PaymentBundle\State\Provider\StateProviderInterface
     */
    private $stateProvider;

    /**
     * @param \Darvin\PaymentBundle\Mailer\Factory\EmailFactoryInterface  $emailFactory  Payment email factory
     * @param \Darvin\MailerBundle\Mailer\MailerInterface                 $mailer        Mailer
     * @param \Darvin\PaymentBundle\Logger\PaymentLoggerInterface         $logger        Payment logger
     * @param \Darvin\PaymentBundle\State\Provider\StateProviderInterface $stateProvider Provider
     */
    public function __construct(
        EmailFactoryInterface $emailFactory,
        MailerInterface $mailer,
        PaymentLoggerInterface $logger,
        StateProviderInterface $stateProvider
    ) {
        $this->emailFactory = $emailFactory;
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->stateProvider = $stateProvider;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.payment.completed' => 'sendEmails',
        ];
    }

    /**
     * @param \Symfony\Component\Workflow\Event\Event $event Event
     *
     * @throws \Darvin\PaymentBundle\State\Exception\UnknownStateException
     */
    public function sendEmails(Event $event): void
    {
        $payment = $event->getSubject();

        if (!$payment instanceof \Darvin\PaymentBundle\Entity\Payment) {
            return;
        }

        $state = $this->stateProvider->getState($payment->getState());

        if ($payment->getClientEmail() !== null && $state->getEmail()->getPublicEmail()->isEnabled()) {
            $publicEmail = null;

            try {
                $publicEmail = $this->emailFactory->createPublicEmail($payment, $state);
            } catch (CantCreateEmailException $ex) {
                $this->logger->saveErrorLog($payment,(string) $ex->getCode(), $ex->getMessage());
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
                $this->logger->saveErrorLog($payment, (string) $ex->getCode(), $ex->getMessage());
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
            $this->logger->saveErrorLog($payment, (string) $ex->getCode(), $ex->getMessage());
        }
    }
}
