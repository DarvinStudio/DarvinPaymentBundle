<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Status\Provider;

use Darvin\PaymentBundle\DBAL\Type\PaymentStatusType;
use Darvin\PaymentBundle\Status\Exception\UnknownStatusException;
use Darvin\PaymentBundle\Status\Model\Email\Email;
use Darvin\PaymentBundle\Status\Model\Email\PublicEmail;
use Darvin\PaymentBundle\Status\Model\Email\ServiceEmail;
use Darvin\PaymentBundle\Status\Model\PaymentStatus;
use http\Exception\UnexpectedValueException;

/**
 * Payment status provider
 */
class StatusProvider implements StatusProviderInterface
{
    /**
     * @var \Darvin\PaymentBundle\Status\Model\PaymentStatus[]|null
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
     * @param string $name   Status name
     * @param array  $config Config of status
     *
     * @throws \Darvin\PaymentBundle\Status\Exception\UnknownStatusException
     */
    public function addConfig(string $name, array $config): void
    {
        if (!PaymentStatusType::isValueExist($name)) {
            throw new UnknownStatusException($name);
        }

        $this->configs[$name] = $config;
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
                            $config['public']['enabled'],
                            $config['public']['template']
                        ),
                        new ServiceEmail(
                            $config['service']['enabled'],
                            $config['service']['template']
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

        throw new UnknownStatusException($name);
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
