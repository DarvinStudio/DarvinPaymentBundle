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
use Darvin\PaymentBundle\Event\State\ChangedEvent;
use Darvin\PaymentBundle\Mailer\Factory\EmailFactoryInterface;
use Darvin\PaymentBundle\State\Provider\StateProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
     * @var \Darvin\PaymentBundle\State\Provider\StateProviderInterface
     */
    private $stateProvider;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Psr\Log\LoggerInterface|null
     */
    private $logger;

    /**
     * @param \Darvin\PaymentBundle\Mailer\Factory\EmailFactoryInterface  $emailFactory  Payment email factory
     * @param \Darvin\MailerBundle\Mailer\MailerInterface                 $mailer        Mailer
     * @param \Darvin\PaymentBundle\State\Provider\StateProviderInterface $stateProvider Provider
     * @param \Doctrine\ORM\EntityManagerInterface                        $entityManager EntityManagerInterface
     */
    public function __construct(
        EmailFactoryInterface $emailFactory,
        MailerInterface $mailer,
        StateProviderInterface $stateProvider,
        EntityManagerInterface $entityManager
    ) {
        $this->emailFactory = $emailFactory;
        $this->mailer = $mailer;
        $this->stateProvider = $stateProvider;
        $this->entityManager = $entityManager;
    }

    /**
     * @param \Psr\Log\LoggerInterface|null $logger Logger
     */
    public function setLogger(?LoggerInterface $logger): void
    {
        $this->logger = $logger;
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
     * @param \Darvin\PaymentBundle\Event\State\ChangedEvent $event Event
     *
     * @throws \Darvin\PaymentBundle\State\Exception\UnknownStateException
     */
    public function sendEmails(ChangedEvent $event): void
    {
        $state = $this->stateProvider->getState($event->getState());
        $order = $this->entityManager->find($event->getOrderClass(), $event->getOrderId());

        if ($event->getClientEmail() !== null &&
            $state->getEmail()->getPublicEmail()->isEnabled()) {

            try {
                $email = $this->emailFactory->createPublicEmail($order, $state, $event->getClientEmail());
                $this->mailer->mustSend($email);
            } catch (CantCreateEmailException | MailerException $ex) {
                $this->addErrorLog($ex);
            }
        }

        if ($state->getEmail()->getServiceEmail()->isEnabled()) {
            try {
                $email = $this->emailFactory->createServiceEmail($order, $state);
                $this->mailer->mustSend($email);
            } catch (CantCreateEmailException | MailerException $ex) {
                $this->addErrorLog($ex);
            }
        }
    }

    /**
     * @param \Exception $exception Logger
     */
    private function addErrorLog(\Exception $exception): void
    {
        if (null !== $this->logger) {
            $this->logger->error($exception->getMessage());
        }
    }
}
