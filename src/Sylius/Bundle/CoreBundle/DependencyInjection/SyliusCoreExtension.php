<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DependencyInjection;

use FOS\ElasticaBundle\DependencyInjection\Configuration as FosElasticaConfiguration;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Bundle\CoreBundle\EventListener\ElasticaProductListener;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
final class SyliusCoreExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * @var array
     */
    private static $bundles = [
        'sylius_addressing',
        'sylius_attribute',
        'sylius_channel',
        'sylius_currency',
        'sylius_customer',
        'sylius_inventory',
        'sylius_locale',
        'sylius_order',
        'sylius_payment',
        'sylius_payum',
        'sylius_product',
        'sylius_promotion',
        'sylius_review',
        'sylius_shipping',
        'sylius_taxation',
        'sylius_taxonomy',
        'sylius_user',
        'sylius_variation',
    ];

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $loader->load('services.xml');

        $env = $container->getParameter('kernel.environment');
        if ('test' === $env || 'test_cached' === $env) {
            $loader->load('test_services.xml');
        }

        $this->overwriteRuleFactory($container);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $config = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);

        foreach ($container->getExtensions() as $name => $extension) {
            if (in_array($name, self::$bundles, true)) {
                $container->prependExtensionConfig($name, ['driver' => $config['driver']]);
            }
        }

        $container->prependExtensionConfig('sylius_theme', ['context' => 'sylius.theme.context.channel_based']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $this->prependHwiOauth($container, $loader);

        $this->prependElasticaProductListener($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function overwriteRuleFactory(ContainerBuilder $container)
    {
        $baseFactoryDefinition = new Definition(Factory::class, [new Parameter('sylius.model.promotion_rule.class')]);
        $promotionRuleFactoryClass = $container->getParameter('sylius.factory.promotion_rule.class');
        $decoratedPromotionRuleFactoryDefinition = new Definition($promotionRuleFactoryClass, [$baseFactoryDefinition]);

        $container->setDefinition('sylius.factory.promotion_rule', $decoratedPromotionRuleFactoryDefinition);
    }

    /**
     * @param ContainerBuilder $container
     * @param LoaderInterface $loader
     */
    private function prependHwiOauth(ContainerBuilder $container, LoaderInterface $loader)
    {
        if (!$container->hasExtension('hwi_oauth')) {
            return;
        }

        $loader->load('services/integrations/hwi_oauth.xml');
    }

    /**
     * @param ContainerBuilder $container
     */
    private function prependElasticaProductListener(ContainerBuilder $container)
    {
        if (!$container->hasExtension('fos_elastica') || !$container->hasExtension('sylius_grid')) {
            return;
        }

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
