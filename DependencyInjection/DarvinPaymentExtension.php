<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\DependencyInjection;

use Darvin\PaymentBundle\DBAL\Type\PaymentStatusType;
use Darvin\Utils\DependencyInjection\ConfigInjector;
use Darvin\Utils\DependencyInjection\ConfigLoader;
use Darvin\Utils\DependencyInjection\ExtensionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class DarvinPaymentExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        (new ConfigInjector($container))->inject($this->processConfiguration(new Configuration(), $configs), $this->getAlias());

        (new ConfigLoader($container, __DIR__.'/../Resources/config/services'))->load([
            'configuration',
            'controller',
            'gateway_factory',
            'mailer' => ['callback' => static function () use ($config): bool {
                return $config['mailer']['enabled'];
            }],
            'payment_manager',
            'status',
            'token',
            'url_builder',
            'bridges/telr' => ['callback' => static function () use ($config): bool {
                return $config['bridges']['telr']['enabled'];
            }],
            'bridges/sberbank' => ['callback' => static function () use ($config): bool {
                return $config['bridges']['sberbank']['enabled'];
            }],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        (new ExtensionConfigurator($container, __DIR__.'/../Resources/config/app'))->configure('doctrine');

        $bundles = $container->getParameter('kernel.bundles');

        $container->prependExtensionConfig($this->getAlias(), [
            'mailer' => [
                'enabled'          => isset($bundles['DarvinMailerBundle']),
                'payment_statuses' => $this->initPaymentStatuses(),
            ],
        ]);
    }

    /**
     * @return array
     */
    private function initPaymentStatuses(): array
    {
        $data = [];

        foreach (PaymentStatusType::getChoices() as $choice) {
            $data[$choice] = [
                'public' => [
                    'enabled' => false,
                ],
                'service' => [
                    'enabled' => false,
                ],
            ];
        }

        $data[PaymentStatusType::PAID] = [
            'public' => [
                'enabled' => true,
            ],
            'service' => [
                'enabled' => true,
            ],
        ];

        return $data;
    }
}
