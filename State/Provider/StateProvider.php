<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\State\Provider;

use Darvin\PaymentBundle\DBAL\Type\PaymentStateType;
use Darvin\PaymentBundle\State\Model\Email\Email;
use Darvin\PaymentBundle\State\Model\Email\PublicEmail;
use Darvin\PaymentBundle\State\Model\Email\ServiceEmail;
use Darvin\PaymentBundle\State\Model\State;

/**
 * Payment state provider
 */
class StateProvider implements StateProviderInterface
{
    /**
     * @var \Darvin\PaymentBundle\State\Model\State[]|null
     */
    private $states;

    /**
     * @var array
     */
    private $configs;

    public function __construct()
    {
        $this->configs = [];
        $this->states = [];
    }

    /**
     * @param string $name   State name
     * @param array  $config Config of state
     *
     * @throws \InvalidArgumentException
     */
    public function addConfig(string $name, array $config): void
    {
        if (!PaymentStateType::isValueExist($name)) {
            throw new \InvalidArgumentException(sprintf('State "%s" is not exist', $name));
        }

        $this->configs[$name] = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllStates(): array
    {
        if (0 === count($this->states)) {
            foreach ($this->configs as $name => $config) {

                $this->states[$name] = new State(
                    $name,
                    new Email(
                        new PublicEmail(
                            $config['public']['enabled'],
                            $config['public']['template'],
                            $name
                        ),
                        new ServiceEmail(
                            $config['service']['enabled'],
                            $config['service']['template'],
                            $name
                        )
                    )
                );
            }
        }

        return $this->states;
    }

    /**
     * {@inheritDoc}
     */
    public function getState(string $name): State
    {
        foreach ($this->getAllStates() as $state) {
            if ($state->getName() === $name) {
                return $state;
            }
        }

        throw new \InvalidArgumentException(sprintf('Unknown state  "%s"', $name));
    }

    /**
     * {@inheritDoc}
     */
    public function hasState(?string $name): bool
    {
        if (null === $name) {
            return false;
        }

        foreach ($this->getAllStates() as $type) {
            if ($type->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllStateNames(): array
    {
        return array_keys($this->getAllStates());
    }
}
