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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('darvin_payment');

        $builder->getRootNode()
            ->children()
                ->scalarNode('default_currency')->defaultValue('RUB')->cannotBeEmpty()->end()
                ->scalarNode('default_gateway')->defaultNull()->end()
                ->booleanNode('auto_approval')->defaultTrue()->end()
                ->booleanNode('pre_authorize')->defaultFalse()->end()
                ->booleanNode('refund')->defaultFalse()->end()
                ->arrayNode('mailer')->canBeDisabled()->end()
                ->arrayNode('bridges')->useAttributeAsKey('name')
                    ->prototype('array')->canBeDisabled()
                        ->children()
                            ->arrayNode('parameters')
                                ->children()
                                    ->arrayNode('gateway')->prototype('variable')->end()->end()
                                    ->arrayNode('receipt')->prototype('variable')->end()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('states')->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('emails')
                                ->children()
                                    ->arrayNode('public')->canBeDisabled()
                                        ->children()
                                            ->scalarNode('template')->defaultValue('@DarvinPayment/email/public.html.twig')->cannotBeEmpty()->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('service')->canBeDisabled()
                                        ->children()
                                            ->scalarNode('template')->defaultValue('@DarvinPayment/email/service.html.twig')->cannotBeEmpty();

        return $builder;
    }
}
