<?php

declare(strict_types=1);

namespace Eniams\SafeMigrationsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('safe_migrations');

        /* @phpstan-ignore-next-line */
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('critical_tables')->canBeUnset()
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('excluded_statements')->canBeUnset()
                    ->scalarPrototype()->end()
                ->end()
                ->scalarNode('migrations_path')->isRequired()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}