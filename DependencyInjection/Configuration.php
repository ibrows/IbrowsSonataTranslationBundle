<?php

namespace Ibrows\SonataTranslationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ibrows_sonata_translation');

        $this->addEditableSection($rootNode);

        return $treeBuilder;
    }
    
    protected function addEditableSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('defaultDomain')->defaultValue('messages')->end()
                ->arrayNode('defaultSelections')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('nonTranslatedOnly')->defaultFalse()->end()
                    ->end()
                ->end()
                ->arrayNode('emptyPrefixes')
                    ->defaultValue(array('__', 'new_', ''))
                    ->prototype('array')->end()
                ->end()
                ->arrayNode('editable')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('mode')->defaultValue('inline')->end()
                        ->scalarNode('type')->defaultValue('textarea')->end()
                        ->scalarNode('emptytext')->defaultValue('Empty')->end()
                        ->scalarNode('placement')->defaultValue('top')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
