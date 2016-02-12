<?php

namespace Ku\Bundle\WsseServerBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('wsse_server');

        $rootNode->children()
            ->arrayNode('applications')
                ->requiresAtLeastOneElement()
                ->beforeNormalization()
                    ->always(function($v){
                        foreach ((array)$v as $name => $data) {
                            $v[$name]['name'] = $name;
                        }

                        return $v;
                    })
                    ->ifNull()
                    ->thenInvalid('Debe especificar al menos una conexión para el wsse en "wsse_server.applications"')
                ->end()
                ->useAttributeAsKey('username', false)
                ->prototype('array')
                    ->children()
                        ->scalarNode('name')->end()
                        ->scalarNode('username')->isRequired()->end()
                        ->scalarNode('password')->isRequired()->end()
                        ->variableNode('parameters')->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
