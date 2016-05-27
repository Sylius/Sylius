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

use FOS\ElasticaBundle\DependencyInjection\Configuration as FosElasticaConfiguration;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Bundle\SearchBundle\DependencyInjection\Configuration as SyliusSearchConfiguration;
use Sylius\Bundle\SearchBundle\Extension\Doctrine\MatchAgainstFunction;
use Sylius\Bundle\SearchBundle\Listener\ElasticaProductListener;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class SyliusSearchExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $loader->load('services.xml');

        $container->setParameter('sylius_search.config', $config);

        $container->setAlias('sylius_search.command.indexer', sprintf('sylius.search.%s.indexer', ucfirst($config['engine'])));
        $container->setAlias('sylius_search.finder', sprintf('sylius_search.%s.finder', ucfirst($config['engine'])));

        $container->setAlias('sylius_search.query.logger', sprintf('sylius_search.%s.query.logger', ucfirst($config['query_logger']['engine'])));
        $container->setParameter('sylius_search.query.logger.enabled', $config['query_logger']['enabled']);

        $container->setParameter('sylius_search.request.method', $config['request_method']);
        $container->setParameter('sylius_search.search.template', $config['search_form_template']);
        $container->setParameter('sylius_search.pre_search_filter.enabled', $config['filters']['pre_search_filter']['enabled']);
        $container->setParameter('sylius_search.pre_search_filter.taxon', $config['filters']['pre_search_filter']['taxon']);

        $container->setParameter('sylius_search.custom.accessor.class', $config['custom_accessor']);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $this->prependElasticaProductListener($container);
        $this->prependDoctrineOrm($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function prependElasticaProductListener(ContainerBuilder $container)
    {
        if (!$container->hasExtension('fos_elastica') || !$container->hasExtension('sylius_search')) {
            return;
        }

        $configuration = new SyliusSearchConfiguration();
        $processor = new Processor();
        $syliusSearchConfig = $processor->processConfiguration($configuration, $container->getExtensionConfig('sylius_search'));
        $engine = $syliusSearchConfig['engine'];

        if ($engine === 'elasticsearch') {
            $configuration = new FosElasticaConfiguration(false);
            $processor = new Processor();
            $elasticaConfig = $processor->processConfiguration($configuration, $container->getExtensionConfig('fos_elastica'));

            foreach ($elasticaConfig['indexes'] as $index => $config) {
                if (array_key_exists('product', $config['types'])) {
                    $elasticaProductListenerDefinition = new Definition(ElasticaProductListener::class);
                    $elasticaProductListenerDefinition->addArgument(new Reference('fos_elastica.object_persister.' . $index . '.product'));
                    $elasticaProductListenerDefinition->addTag('doctrine.event_subscriber');

                    $container->setDefinition('sylius_product.listener.index_' . $index . '.product_update', $elasticaProductListenerDefinition);
                }
            }
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    private function prependDoctrineOrm(ContainerBuilder $container)
    {
        if (!$container->hasExtension('doctrine')) {
            return;
        }

        $container->prependExtensionConfig('doctrine', [
            'orm' => [
                'entity_managers' => [
                    'default' => [
                        'dql' => [
                            'string_functions' => [
                                'MATCH' => MatchAgainstFunction::class,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
