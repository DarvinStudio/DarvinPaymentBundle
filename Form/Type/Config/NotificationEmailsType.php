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

use Darvin\PaymentBundle\Mailer\Provider\StatusProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Configuration notification emails form type
 */
class NotificationEmailsType extends AbstractType
{
    /**
     * @var \Darvin\PaymentBundle\Mailer\Provider\StatusProviderInterface
     */
    private $statusProvider;

    /**
     * @param \Darvin\PaymentBundle\Mailer\Provider\StatusProviderInterface $statusProvider Payment status provider
     */
    public function __construct(StatusProviderInterface $statusProvider)
    {
        $this->statusProvider = $statusProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $statusProvider = $this->statusProvider;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) use ($statusProvider): void {
            foreach (array_keys($event->getData()) as $name) {
                if (!$statusProvider->hasStatus($name)) {
                    continue;
                }

                $event->getForm()->add($name, CollectionType::class, [
                    'label'         => $statusProvider->getStatus($name)->getTitle(),
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
