<?php declare(strict_types=1);
/**
 * @author    Alexander Volodin <mr-stanlik@yandex.ru>
 * @copyright Copyright (c) 2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\EventListener\ChangedState;

use Darvin\PaymentBundle\DBAL\Type\PaymentStateType;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class payment events subscriber
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
     * @param \Psr\Log\LoggerInterface                            $logger    Logger
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator Translator
     */
    public function __construct(
        LoggerInterface $logger,
        TranslatorInterface $translator
    ){
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.payment.completed' => 'log',
        ];
    }

    /**
     * @param \Symfony\Component\Workflow\Event\Event $event Event
     */
    public function log(Event $event): void
    {
        $payment = $event->getSubject();

        if (!$payment instanceof \Darvin\PaymentBundle\Entity\Payment) {
            return;
        }

        $this->logger->info(
            $this->translator->trans('payment.log.info.changed_state', [
                '%state%' => $this->translator->trans(PaymentStateType::getReadableValue($payment->getState()), [], 'admin'),
            ]),
            ['payment' => $payment]
        );
    }
}
