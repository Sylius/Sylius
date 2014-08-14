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
use Symfony\Component\DependencyInjection\Loader;
use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;

/**
 * Class SyliusSearchExtension
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class SyliusSearchExtension extends AbstractResourceExtension
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

        $container->setParameter('sylius_search.config', $config);

        $commandClass = sprintf('sylius.search.%s.indexer', $config['driver']);
        $container->setAlias('sylius_search.command.indexer', $commandClass);

        $finderClass = sprintf('Sylius\Bundle\SearchBundle\Finder\%sFinder', $config['driver']);
        $container->setParameter('sylius_search.finder', $finderClass);

        $container->setParameter('sylius_search.search.template', $config['search_form_template']);
        $container->setParameter('sylius_search.pre_search_filter.enabled', $config['filters']['pre_search_filter']['enabled']);
        $container->setParameter('sylius_search.pre_search_filter.taxon', $config['filters']['pre_search_filter']['taxonomy']);
    }

}