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

use Darvin\PaymentBundle\DBAL\Type\PaymentStateType;
use Darvin\PaymentBundle\Receipt\ReceiptFactoryInterface;
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
    public const TAG_RECEIPT_FACTORY = 'darvin_payment.receipt_factory';

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->registerForAutoconfiguration(ReceiptFactoryInterface::class)->addTag(self::TAG_RECEIPT_FACTORY);

        (new ConfigInjector($container))->inject($this->processConfiguration(new Configuration(), $configs), $this->getAlias());

        (new ConfigLoader($container, __DIR__.'/../Resources/config/services'))->load([
            'configuration',
            'controller',
            'gateway',
            'mailer' => ['callback' => static function () use ($config): bool {
                return $config['mailer']['enabled'];
            }],
            'payment',
            'state',
            'url_builder',
            'bridges/telr' => ['callback' => static function () use ($config): bool {
                return $config['bridges']['telr']['enabled'] ?? false;
            }],
            'bridges/sberbank' => ['callback' => static function () use ($config): bool {
                return $config['bridges']['sberbank']['enabled'] ?? false;
            }],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        (new ExtensionConfigurator($container, __DIR__.'/../Resources/config/app'))->configure('doctrine');
        (new ExtensionConfigurator($container, __DIR__.'/../Resources/config/app'))->configure('workflow');
        (new ExtensionConfigurator($container, __DIR__.'/../Resources/config/app'))->configure('darvin_admin');

        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['MonologBundle'])) {
            (new ExtensionConfigurator($container, __DIR__.'/../Resources/config/app'))->configure('monolog');
        }

        $container->prependExtensionConfig($this->getAlias(), [
            'mailer' => [
                'enabled' => isset($bundles['DarvinMailerBundle']),
                'states'  => $this->initPaymentStates(),
            ],
        ]);
    }

    /**
     * @return array
     */
    private function initPaymentStates(): array
    {
        $data = [];

        foreach (PaymentStateType::getChoices() as $choice) {
            $data[$choice] = [
                'public' => [
                    'enabled' => false,
                ],
                'service' => [
                    'enabled' => false,
                ],
            ];
        }

        $data[PaymentStateType::COMPLETED] = [
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
