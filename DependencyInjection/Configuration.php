<?php

namespace Ddeboer\Salesforce\ClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

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
        $rootNode = $treeBuilder->root('ddeboer_salesforce_client');
        $rootNode
            ->children()
                ->scalarNode('wsdl')->isRequired()->end()
                ->scalarNode('username')->isRequired()->end()
                ->scalarNode('password')->isRequired()->end()
                ->scalarNode('token')->isRequired()->end()
                ->arrayNode('classmap')
                    ->defaultValue(array(
                        'DeleteResult'  => 'Ddeboer\Salesforce\ClientBundle\Response\DeleteResult',
                        'QueryResult'   => 'Ddeboer\Salesforce\ClientBundle\Response\QueryResult',
                        'SaveResult'    => 'Ddeboer\Salesforce\ClientBundle\Response\SaveResult',
                        'SearchResult'  => 'Ddeboer\Salesforce\ClientBundle\Response\SearchResult',
                        'sObject'       => 'Ddeboer\Salesforce\ClientBundle\Response\SObject',
                    ))
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
