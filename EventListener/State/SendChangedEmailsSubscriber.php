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
use Darvin\PaymentBundle\Logger\PaymentLoggerInterface;
use Darvin\PaymentBundle\Mailer\Factory\EmailFactoryInterface;
use Darvin\PaymentBundle\State\Provider\StateProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

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
     * @param \Doctrine\ORM\EntityManagerInterface                        $em            EntityManagerInterface
     * @param \Darvin\MailerBundle\Mailer\MailerInterface                 $mailer        Mailer
     * @param \Darvin\PaymentBundle\Logger\PaymentLoggerInterface         $logger        Payment logger
     * @param \Darvin\PaymentBundle\State\Provider\StateProviderInterface $stateProvider Provider
     */
    public function __construct(
        EmailFactoryInterface $emailFactory,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        PaymentLoggerInterface $logger,
        StateProviderInterface $stateProvider
    ) {
        $this->emailFactory = $emailFactory;
        $this->em = $em;
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
        $order = $this->em->find($payment->getOrderEntityClass(), $payment->getOrderId());

        if ($payment->getClientEmail() !== null &&
            $state->getEmail()->getPublicEmail()->isEnabled()) {

            try {
                $email = $this->emailFactory->createPublicEmail($order, $state, $payment->getClientEmail());
                $this->mailer->mustSend($email);
            } catch (CantCreateEmailException | MailerException $ex) {
                $this->logger->saveErrorLog($payment,(string) $ex->getCode(), $ex->getMessage());
            }
        }

        if ($state->getEmail()->getServiceEmail()->isEnabled()) {
            try {
                $email = $this->emailFactory->createServiceEmail($order, $state);
                $this->mailer->mustSend($email);
            } catch (CantCreateEmailException | MailerException $ex) {
                $this->logger->saveErrorLog($payment, (string) $ex->getCode(), $ex->getMessage());
            }
        }
    }
}
