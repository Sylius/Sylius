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

use Sylius\Bundle\ResourceBundle\DependencyInjection\SyliusResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\Definition\Processor;

/**
 * Core extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusCoreExtension extends SyliusResourceExtension implements PrependExtensionInterface
{
    /**
     * @var array
     */
    private $bundles = array(
        'sylius_addressing',
        'sylius_inventory',
        'sylius_money',
        'sylius_payments',
        'sylius_payum',
        'sylius_product',
        'sylius_promotions',
        'sylius_sales',
        'sylius_settings',
        'sylius_shipping',
        'sylius_taxation',
        'sylius_taxonomies',
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $driver = $config['driver'];

        $this->loadDatabaseDriver($driver, $loader);

        $container->setParameter('sylius_core.driver', $driver);
        $container->setParameter('sylius_core.driver.'.$driver, true);

        $loader->load('services.xml');

        $classes = $config['classes'];

        $this->mapClassParameters($classes, $container);

        if ($container->hasParameter('sylius.config.classes')) {
            $classes = array_merge($classes, $container->getParameter('sylius.config.classes'));
        }

        $container->setParameter('sylius.config.classes', $classes);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $container->getExtensionConfig($this->getAlias()));

        foreach ($container->getExtensions() as $name => $extension) {
            if (in_array($name, $this->bundles)) {
                $container->prependExtensionConfig($name, array('driver' => $config['driver']));
            }
        }
    }
}
