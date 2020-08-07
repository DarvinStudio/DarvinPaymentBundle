<?php declare(strict_types=1);
/**
 * @author    Alexander Volodin <mr-stanlik@yandex.ru>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\EventListener;

use Darvin\MailerBundle\Factory\Exception\CantCreateEmailException;
use Darvin\MailerBundle\Mailer\Exception\MailerException;
use Darvin\MailerBundle\Mailer\MailerInterface;
use Darvin\MailerBundle\Model\EmailType;
use Darvin\PaymentBundle\DBAL\Type\PaymentStatusType;
use Darvin\PaymentBundle\Event\ChangedStatusEvent;
use Darvin\PaymentBundle\Event\PaymentEvents;
use Darvin\PaymentBundle\Mailer\Factory\EmailFactoryInterface;
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
     * @param \Darvin\PaymentBundle\Mailer\Factory\EmailFactoryInterface $emailFactory Payment email factory
     * @param \Darvin\MailerBundle\Mailer\MailerInterface                $mailer       Mailer
     */
    public function __construct(EmailFactoryInterface $emailFactory, MailerInterface $mailer)
    {
        $this->emailFactory = $emailFactory;
        $this->mailer = $mailer;
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
     */
    public function sendEmails(ChangedStatusEvent $event): void
    {
        $payment = $event->getPayment();

        // TODO Отправка email
    }
}
