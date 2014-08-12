<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

/**
 * Class SyliusSearchExtension
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class SyliusSearchExtension extends Extension
{

    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('search', $config['driver']);
        $container->setParameter('sylius_search.config', $config);

        $searchClass = 'Sylius\Bundle\SearchBundle\Searcher\\'.$config['driver'].'Searcher';
        $finderClass = 'Sylius\Bundle\SearchBundle\Finder\\'.$config['driver'].'Finder';

        $commandClass = sprintf('sylius.search.%s.indexer', $config['driver']);
        $container->setAlias('sylius_search.command', $commandClass);

        $container->setParameter('sylius_search.engine', $searchClass);
        $container->setParameter('sylius_search.finder', $finderClass);
        $container->setParameter('sylius_search.command', $commandClass);
        $container->setParameter('sylius_search.form', $config['form']);
        $container->setParameter('sylius_search.filter.enabled', $config['filters']['search_filter']['enabled']);
        $container->setParameter('sylius_search.filter.taxonomy', $config['filters']['search_filter']['taxonomy']);
    }

}