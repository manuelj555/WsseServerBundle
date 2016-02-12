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

        $defaultRepositoryService = 'wsse_server.application.default_application_repository';

        $invalidApplicationsMessage = <<<TEXT
Debe especificar al menos una conexión para el wsse ya que usted está utilizando el servicio de repositorio de aplicaciones por defecto del bundle, el cual necesita que se indiquen las conexiones en la configuración de "applications"
TEXT;

        $rootNode
            ->validate()
                ->ifTrue(function($v) use ($defaultRepositoryService){
                    return $defaultRepositoryService == $v['application_repository_service']
                        && 0 == count($v['applications']);
                })
                ->thenInvalid($invalidApplicationsMessage)
            ->end()
            ->children()
                ->scalarNode('application_repository_service')
                    ->defaultValue($defaultRepositoryService)
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('applications')
                    ->beforeNormalization()
                        ->always(function($v){
                            foreach ((array)$v as $name => $data) {
                                $v[$name]['name'] = $name;
                            }

                            return (array)$v;
                        })
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
