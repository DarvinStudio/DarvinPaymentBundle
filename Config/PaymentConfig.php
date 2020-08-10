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
use Darvin\PaymentBundle\Mailer\Provider\StatusProviderInterface;

/**
 * Payment configuration
 */
class PaymentConfig extends AbstractConfiguration implements PaymentConfigInterface
{
    /**
     * @var \Darvin\PaymentBundle\Mailer\Provider\StatusProviderInterface
     */
    private $statusProvider;

    /**
     * @var bool
     */
    private $mailerEnabled;

    /**
     * @param \Darvin\PaymentBundle\Mailer\Provider\StatusProviderInterface $statusProvider Provider
     * @param bool                                                          $mailerEnabled  Is mailer enabled
     */
    public function __construct(StatusProviderInterface $statusProvider, bool $mailerEnabled)
    {
        $this->statusProvider = $statusProvider;
        $this->mailerEnabled = $mailerEnabled;
    }

    /**
     * {@inheritDoc}
     */
    public function getModel(): iterable
    {
        if ($this->mailerEnabled) {
            $defaultNotificationEmails = [];

            foreach ($this->statusProvider->getAllStatuses() as $paymentStatus) {
                if ($paymentStatus->getEmail()->getServiceEmail()->isEnabled()) {
                    $defaultNotificationEmails[$paymentStatus->getName()] = [];
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
    public function getEmailsByStatusName(string $name): array
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
