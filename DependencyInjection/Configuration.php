<?php declare(strict_types=1);

namespace Darvin\PaymentBundle\DependencyInjection;

use Darvin\PaymentBundle\Entity\Payment;
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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('darvin_payment');

        $rootNode
            ->children()
                ->scalarNode('default_currency')->defaultValue('RUB')->end()
                ->scalarNode('payment_class')->defaultValue(Payment::class)->end()
                ->scalarNode('default_gateway')->defaultNull()->end()
                ->arrayNode('parameters_bridge')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->useAttributeAsKey('key')
                        ->scalarPrototype()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
