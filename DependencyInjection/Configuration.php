<?php
namespace Kna\MoneyBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('kna_money');
        $root = $treeBuilder->getRootNode();

        $root
            ->children()
                ->scalarNode('locale')
                    ->defaultValue('en')
                ->end()
                ->arrayNode('bitcoin')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('fraction_digits')
                            ->defaultValue(8)
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}