<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Darvin\PaymentBundle\Form\Type\Config;

use Darvin\PaymentBundle\Status\Provider\StatusProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Configuration notification emails form type
 */
class NotificationEmailsType extends AbstractType
{
    /**
     * @var \Darvin\PaymentBundle\Status\Provider\StatusProviderInterface
     */
    private $statusProvider;

    /**
     * @var \Symfony\Contracts\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @param \Darvin\PaymentBundle\Status\Provider\StatusProviderInterface $statusProvider Payment status provider
     * @param \Symfony\Contracts\Translation\TranslatorInterface            $translator     Translator
     */
    public function __construct(StatusProviderInterface $statusProvider, TranslatorInterface $translator)
    {
        $this->statusProvider = $statusProvider;
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {

            foreach (array_keys($event->getData()) as $name) {
                if (!$this->statusProvider->hasStatus($name)) {
                    continue;
                }

                $event->getForm()->add($name, CollectionType::class, [
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'delete_empty'  => true,
                    'entry_type'    => EmailType::class,
                    'entry_options' => [
                        'constraints' => [
                            new NotBlank(),
                            new Email(),
                        ],
                    ],
                    'label' => $this->translator->trans(
                        'configuration.darvin_payment.parameter.notification_for_status',
                        ['%status%' => $this->translator->trans(sprintf('payment.status.%s', $name), [], 'admin')],
                        'admin'
                    ),
                ]);
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getParent(): string
    {
        return CollectionType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix(): string
    {
        return 'darvin_payment_config_notification_emails';
    }
}
