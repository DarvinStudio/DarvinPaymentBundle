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
        $builder = new TreeBuilder('darvin_payment');
        $root = $builder->getRootNode();

        $root
            ->children()
                ->scalarNode('default_currency')->defaultValue('RUB')->end()
                ->scalarNode('default_gateway')->defaultNull()->end()
                ->scalarNode('payment_class')->defaultValue(Payment::class)->end()
                ->arrayNode('bridges')->useAttributeAsKey('name')
                    ->prototype('array')
                        ->prototype('variable')
        ;

        return $builder;
    }
}
