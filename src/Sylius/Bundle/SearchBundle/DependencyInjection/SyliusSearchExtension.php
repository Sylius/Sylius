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

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class SyliusSearchExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $configFiles = [
            'services.xml',
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $container->setParameter('sylius_search.config', $config);

        $container->setAlias('sylius_search.command.indexer', sprintf('sylius.search.%s.indexer', ucfirst($config['engine'])));
        $container->setAlias('sylius_search.finder', sprintf('sylius_search.%s.finder', ucfirst($config['engine'])));

        $container->setAlias('sylius_search.query.logger', sprintf('sylius_search.%s.query.logger', ucfirst($config['query_logger']['engine'])));
        $container->setParameter('sylius_search.query.logger.enabled', $config['query_logger']['enabled']);

        $container->setParameter('sylius_search.request.method', $config['request_method']);
        $container->setParameter('sylius_search.search.template', $config['search_form_template']);
        $container->setParameter('sylius_search.pre_search_filter.enabled', $config['filters']['pre_search_filter']['enabled']);
        $container->setParameter('sylius_search.pre_search_filter.taxon', $config['filters']['pre_search_filter']['taxonomy']);

        $container->setParameter('sylius_search.custom.accessor.class', $config['custom_accessor']);
    }
}
