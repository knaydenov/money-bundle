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
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('kna_money');

        $root
            ->children()
                ->scalarNode('locale')
                    ->defaultValue('en')
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}