<?php declare(strict_types=1);
/**
 * @author    Alexander Volodin <mr-stanlik@yandex.ru>
 * @copyright Copyright (c) 2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\EventListener;

use Darvin\MailerBundle\Factory\Exception\CantCreateEmailException;
use Darvin\MailerBundle\Mailer\MailerInterface;
use Darvin\PaymentBundle\Event\ChangedStatusEvent;
use Darvin\PaymentBundle\Event\PaymentEvents;
use Darvin\PaymentBundle\Mailer\Factory\EmailFactoryInterface;
use Darvin\PaymentBundle\Status\Provider\StatusProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class payment events subscriber
 */
class PaymentEventsSubscriber implements EventSubscriberInterface
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
     * @var \Darvin\PaymentBundle\Status\Provider\StatusProviderInterface
     */
    private $statusProvider;

    /**
     * @param \Darvin\PaymentBundle\Mailer\Factory\EmailFactoryInterface    $emailFactory   Payment email factory
     * @param \Darvin\MailerBundle\Mailer\MailerInterface                   $mailer         Mailer
     * @param \Darvin\PaymentBundle\Status\Provider\StatusProviderInterface $statusProvider Provider
     */
    public function __construct(
        EmailFactoryInterface $emailFactory,
        MailerInterface $mailer,
        StatusProviderInterface $statusProvider
    ) {
        $this->emailFactory = $emailFactory;
        $this->mailer = $mailer;
        $this->statusProvider = $statusProvider;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PaymentEvents::CHANGED_STATUS => 'sendEmails',
        ];
    }

    /**
     * @param \Darvin\PaymentBundle\Event\ChangedStatusEvent $event Event
     *
     * @throws \Darvin\PaymentBundle\Status\Exception\UnknownStatusException
     */
    public function sendEmails(ChangedStatusEvent $event): void
    {
        $payment = $event->getPayment();
        $paymentStatus = $this->statusProvider->getStatus($payment->getStatus());

        if ($paymentStatus->getEmail()->getPublicEmail()->isEnabled()) {
            try {
                $email = $this->emailFactory->createPublicEmail($payment, $paymentStatus);
            } catch (CantCreateEmailException $ex) {
                $email = null;
            }

            if (null !== $email) {
                $this->mailer->send($email);
            }
        }

        if ($paymentStatus->getEmail()->getServiceEmail()->isEnabled()) {
            try {
                $email = $this->emailFactory->createServiceEmail($payment, $paymentStatus);
            } catch (CantCreateEmailException $ex) {
                $email = null;
            }

            if (null !== $email) {
                $this->mailer->send($email);
            }
        }
    }
}
