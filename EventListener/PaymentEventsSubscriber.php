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
use Darvin\MailerBundle\Mailer\Exception\MailerException;
use Darvin\MailerBundle\Mailer\MailerInterface;
use Darvin\PaymentBundle\Event\ChangedStatusEvent;
use Darvin\PaymentBundle\Event\PaymentEvents;
use Darvin\PaymentBundle\Mailer\Factory\EmailFactoryInterface;
use Darvin\PaymentBundle\Status\Provider\StatusProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
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
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Psr\Log\LoggerInterface|null
     */
    private $logger;

    /**
     * @param \Darvin\PaymentBundle\Mailer\Factory\EmailFactoryInterface    $emailFactory   Payment email factory
     * @param \Darvin\MailerBundle\Mailer\MailerInterface                   $mailer         Mailer
     * @param \Darvin\PaymentBundle\Status\Provider\StatusProviderInterface $statusProvider Provider
     * @param \Doctrine\ORM\EntityManagerInterface                          $entityManager  EntityManagerInterface
     */
    public function __construct(
        EmailFactoryInterface $emailFactory,
        MailerInterface $mailer,
        StatusProviderInterface $statusProvider,
        EntityManagerInterface $entityManager
    ) {
        $this->emailFactory = $emailFactory;
        $this->mailer = $mailer;
        $this->statusProvider = $statusProvider;
        $this->entityManager = $entityManager;
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
     * @throws \Darvin\PaymentBundle\Status\Exception\UnknownStatusException
     */
    public function sendEmails(ChangedStatusEvent $event): void
    {
        $paymentStatus = $this->statusProvider->getStatus($event->getStatus());
        $order = $this->entityManager->find($event->getOrderClass(), $event->getOrderId());

        if ($event->getClientEmail() !== null &&
            $paymentStatus->getEmail()->getPublicEmail()->isEnabled()) {

            try {
                $email = $this->emailFactory->createPublicEmail($order, $paymentStatus, $event->getClientEmail());
                $this->mailer->mustSend($email);
            } catch (CantCreateEmailException $ex) {
                $this->addErrorLog($ex);
            } catch (MailerException $ex) {
                $this->addErrorLog($ex);
            }
        }

        if ($paymentStatus->getEmail()->getServiceEmail()->isEnabled()) {
            try {
                $email = $this->emailFactory->createServiceEmail($order, $paymentStatus);
                $this->mailer->mustSend($email);
            } catch (CantCreateEmailException $ex) {
                $this->addErrorLog($ex);
            } catch (MailerException $ex) {
                $this->addErrorLog($ex);
            }
        }
    }

    /**
     * @param \Psr\Log\LoggerInterface|null $logger Logger
     */
    public function setLogger(?LoggerInterface $logger): void
    {
        $this->logger = $logger;
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
