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
     * @var array
     */
    private $configs;

    /**
     * @var \Darvin\PaymentBundle\State\Model\State[]|null
     */
    private $states;

    /**
     * @param array $configs Configs
     */
    public function __construct(array $configs)
    {
        $this->configs = $configs;
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

        throw new \InvalidArgumentException(sprintf('Unknown state "%s".', $name));
    }

    /**
     * {@inheritDoc}
     */
    public function hasState(?string $name): bool
    {
        if (null === $name) {
            return false;
        }
        foreach ($this->getAllStates() as $state) {
            if ($state->getName() === $name) {
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

    /**
     * {@inheritDoc}
     */
    public function getAllStates(): array
    {
        if (null === $this->states) {
            $states = [];

            foreach ($this->configs as $name => $config) {
                if (!PaymentStateType::isValueExist($name)) {
                    throw new \InvalidArgumentException(sprintf('State "%s" does not exist.', $name));
                }

                $states[$name] = new State(
                    $name,
                    new Email(
                        new PublicEmail($config['public']['enabled'], $config['public']['template'], $name),
                        new ServiceEmail($config['service']['enabled'], $config['service']['template'], $name)
                    )
                );
            }

            $this->states = $states;
        }

        return $this->states;
    }
}
