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
use Darvin\OrderBundle\Form\Type\Config\NotificationEmailsType;
use Darvin\OrderBundle\Type\Model\OrderType;
use Darvin\OrderBundle\Type\Provider\OrderTypeProviderInterface;

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
            // TODO написать вставку email для разных событий
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getNotificationEmailsByType(OrderType $type): array
    {
        return $this->__get('notification_emails')[$type->getName()] ?? [];
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'darvin_payment';
    }
}
