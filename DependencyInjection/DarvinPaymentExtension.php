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
use Darvin\PaymentBundle\DependencyInjection\Compiler\AddReceiptFactoriesPass;
use Darvin\PaymentBundle\Receipt\ReceiptFactoryInterface;
use Darvin\PaymentBundle\Payment\Operations;
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
        $container->registerForAutoconfiguration(ReceiptFactoryInterface::class)->addTag(AddReceiptFactoriesPass::TAG_RECEIPT_FACTORY);

        $config = $this->processConfiguration(new Configuration(), $configs);

        (new ConfigInjector($container))->inject($config, $this->getAlias());

        (new ConfigLoader($container, __DIR__.'/../Resources/config/services'))->load([
            'controller',
            'gateway',
            'logger',
            'payment',
            'purchase',
            'receipt',
            'redirect',
            'state',
            'url',

            'admin/common' => ['bundle' => 'DarvinAdminBundle'],
            'admin/approve' => ['bundle' => 'DarvinAdminBundle', 'callback' => static function () use ($config): bool {
                return !$config['auto_approval'];
            }],
            'admin/capture' => ['bundle' => 'DarvinAdminBundle', 'callback' => static function () use ($config): bool {
                return $config['pre_authorize'];
            }],
            'admin/void' => ['bundle' => 'DarvinAdminBundle', 'callback' => static function () use ($config): bool {
                return $config['pre_authorize'];
            }],
            'admin/refund' => ['bundle' => 'DarvinAdminBundle', 'callback' => static function () use ($config): bool {
                return $config['refund'];
            }],

            'bridge/sberbank' => ['callback' => static function () use ($config): bool {
                return $config['bridges']['sberbank']['enabled'] ?? false;
            }],
            'bridge/telr' => ['callback' => static function () use ($config): bool {
                return $config['bridges']['telr']['enabled'] ?? false;
            }],

            'config' => ['bundle' => 'DarvinConfigBundle'],

            'mailer' => ['callback' => static function () use ($config): bool {
                return $config['mailer']['enabled'];
            }],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        (new ExtensionConfigurator($container, __DIR__.'/../Resources/config/app'))->configure([
            'darvin_admin',
            'doctrine',
            'framework',
            'monolog',
        ]);

        $container->prependExtensionConfig($this->getAlias(), [
            'mailer' => [
                'enabled' => array_key_exists('DarvinMailerBundle', $container->getParameter('kernel.bundles')),
            ],
        ]);

        $container->prependExtensionConfig($this->getAlias(), [
            'states' => $this->buildStatesConfig(),
        ]);

        $container->prependExtensionConfig('framework', [
            'workflows' => [
                'payment' => [
                    'initial_marking' => PaymentStateType::APPROVAL,
                    'places'          => array_values(PaymentStateType::getChoices()),
                    'transitions'     => $this->buildWorkflowTransitions(),
                ],
            ],
        ]);
    }

    /**
     * @return array
     */
    private function buildStatesConfig(): array
    {
        $config = [];

        foreach (PaymentStateType::getChoices() as $state) {
            $config[$state] = [
                'emails' => [
                    'public' => [
                        'enabled' => false,
                    ],
                    'service' => [
                        'enabled' => true,
                    ],
                ],
            ];
        }

        $config[PaymentStateType::COMPLETED] = [
            'emails' => [
                'public' => [
                    'enabled' => true,
                ],
                'service' => [
                    'enabled' => true,
                ],
            ],
        ];

        return $config;
    }

    /**
     * @return array
     */
    private function buildWorkflowTransitions(): array
    {
        $transitions = [];

        foreach (Operations::OPERATIONS as $name => [$from, $to]) {
            $transitions[$name] = [
                'from' => $from,
                'to'   => $to,
            ];
        }

        return $transitions;
    }
}
