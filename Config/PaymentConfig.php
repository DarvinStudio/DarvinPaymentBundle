<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Config;

use Darvin\ConfigBundle\Configuration\AbstractConfiguration;
use Darvin\ConfigBundle\Parameter\ParameterModel;
use Darvin\PaymentBundle\Form\Type\Config\NotificationEmailsType;
use Darvin\PaymentBundle\State\Provider\StateProviderInterface;

/**
 * Payment configuration
 */
class PaymentConfig extends AbstractConfiguration implements PaymentConfigInterface
{
    /**
     * @var \Darvin\PaymentBundle\State\Provider\StateProviderInterface
     */
    private $stateProvider;

    /**
     * @var bool
     */
    private $mailerEnabled;

    /**
     * @param \Darvin\PaymentBundle\State\Provider\StateProviderInterface $stateProvider Payment state provider
     * @param bool                                                        $mailerEnabled Is mailer enabled
     */
    public function __construct(StateProviderInterface $stateProvider, bool $mailerEnabled)
    {
        $this->stateProvider = $stateProvider;
        $this->mailerEnabled = $mailerEnabled;
    }

    /**
     * {@inheritDoc}
     */
    public function getModel(): iterable
    {
        if ($this->mailerEnabled) {
            $defaultNotificationEmails = [];

            foreach ($this->stateProvider->getAllStates() as $state) {
                if ($state->getServiceEmail()->isEnabled()) {
                    $defaultNotificationEmails[$state->getName()] = [];
                }
            }

            yield new ParameterModel('notification_emails', ParameterModel::TYPE_ARRAY, $defaultNotificationEmails, [
                'form' => [
                    'type' => NotificationEmailsType::class,
                ],
            ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getEmailsByStateName(string $name): array
    {
        return $this->__get('notification_emails')[$name] ?? [];
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'darvin_payment';
    }
}
