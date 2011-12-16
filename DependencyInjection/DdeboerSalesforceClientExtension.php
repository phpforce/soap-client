<?php

namespace Ddeboer\Salesforce\ClientBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DdeboerSalesforceClientExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        foreach ($config as $key => $value) {
            $container->setParameter('ddeboer_salesforce_client.' . $key, $value);
        }

        $clientBuilder = new DefinitionDecorator('besimple.soap.client.builder');
        $clientBuilder->setClass('Ddeboer\Salesforce\ClientBundle\Soap\SoapClientBuilder');
        $container->setDefinition('ddeboer_salesforce_client.soap.client.builder', $clientBuilder);

        $clientBuilder->replaceArgument(0, $config['wsdl']);
        $clientBuilder->replaceArgument(2, new Reference('besimple.soap.classmap.salesforce'));
        $clientBuilder->addMethodCall('withSingleElementArrays');

        $definition = new DefinitionDecorator('besimple.soap.classmap');
        $container->setDefinition('besimple.soap.classmap.salesforce', $definition);
        $definition->setMethodCalls(array(
            array('set', array($config['classmap'])),
        ));

        $converters = $container->getDefinition('ddeboer_salesforce_client.soap.converter.collection');
        $converters->addMethodCall('set', array(array(
            new Reference('ddeboer_salesforce_client.soap.converter.date_time'),
            new Reference('besimple.soap.converter.date')
        )));
        $clientBuilder->replaceArgument(3, new Reference('ddeboer_salesforce_client.soap.converter.collection'));
    }
}
