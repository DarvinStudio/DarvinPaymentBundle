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

use Darvin\PaymentBundle\DBAL\Type\PaymentStateType;
use Darvin\PaymentBundle\State\Event\ChangedStateEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class payment events subscriber for log about changed state
 */
class LogSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @param \Psr\Log\LoggerInterface                           $logger     Logger
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator Translator
     */
    public function __construct(LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ChangedStateEvent::class => 'log',
        ];
    }

    /**
     * @param \Darvin\PaymentBundle\State\Event\ChangedStateEvent $event
     */
    public function log(ChangedStateEvent $event): void
    {
        $payment = $event->getPayment();

        $stateTitle = $this->translator->trans(PaymentStateType::getReadableValue($payment->getState()), [], 'admin');

        $this->logger->info(
            $this->translator->trans('info.changed_state', ['%state%' => $stateTitle], 'payment_event'),
            ['payment' => $payment]
        );
    }
}
