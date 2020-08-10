<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Mailer\Provider;

use Darvin\PaymentBundle\Mailer\Model\Email\Email;
use Darvin\PaymentBundle\Mailer\Model\Email\PublicEmail;
use Darvin\PaymentBundle\Mailer\Model\Email\ServiceEmail;
use Darvin\PaymentBundle\Mailer\Model\PaymentStatus;

/**
 * Payment status provider
 */
class StatusProvider implements StatusProviderInterface
{
    /**
     * @var \Darvin\PaymentBundle\Mailer\Model\PaymentStatus[]|null
     */
    private $statuses;

    /**
     * @var array
     */
    private $configs;

    public function __construct()
    {
        $this->configs = [];
        $this->statuses = [];
    }

    /**
     * @param array $configs
     */
    public function addConfigs(array $configs): void
    {
        $this->configs = $configs;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllStatuses(): array
    {
        if (empty($this->statuses)) {
            foreach ($this->configs as $name => $config) {

                $this->statuses[$name] = new PaymentStatus(
                    $name,
                    new Email(
                        new PublicEmail(
                            $config['email']['public']['enabled'],
                            $config['email']['public']['template']
                        ),
                        new ServiceEmail(
                            $config['email']['service']['enabled'],
                            $config['email']['service']['template']
                        )
                    )
                );
            }
        }

        return $this->statuses;
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus(string $name): PaymentStatus
    {
        foreach ($this->getAllStatuses() as $paymentStatus) {
            if ($paymentStatus->getName() === $name) {
                return $paymentStatus;
            }
        }

        throw new \InvalidArgumentException(sprintf('Payment status "%s" does not exist.', $name));
    }

    /**
     * {@inheritDoc}
     */
    public function hasStatus(?string $name): bool
    {
        if (null === $name) {
            return false;
        }
        foreach ($this->getAllStatuses() as $type) {
            if ($type->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllStatusNames(): array
    {
        return array_keys($this->getAllStatuses());
    }
}
