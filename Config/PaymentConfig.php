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
use Darvin\PaymentBundle\DBAL\Type\PaymentStatusType;

/**
 * Payment configuration
 */
class PaymentConfig extends AbstractConfiguration implements PaymentConfigInterface
{
    /**
     * @var bool
     */
    private $mailerEnabled;

    /**
     * @param bool $mailerEnabled     Is mailer enabled
     */
    public function __construct(bool $mailerEnabled)
    {
        $this->mailerEnabled = $mailerEnabled;
    }

    /**
     * {@inheritDoc}
     */
    public function getModel(): iterable
    {
        if ($this->mailerEnabled) {
            foreach (PaymentStatusType::getChoices() as $choice) {
                // TODO config emails
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getNotificationEmailsByStatus(string $status): array
    {
        return $this->__get('notification_emails')[$status] ?? [];
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'darvin_payment';
    }
}
