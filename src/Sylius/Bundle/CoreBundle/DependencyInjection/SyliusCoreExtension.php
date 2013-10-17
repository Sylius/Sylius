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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\Definition\Processor;
use Sylius\Bundle\CoreBundle\SyliusCoreBundle;

/**
 * Core extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusCoreExtension extends Extension implements PrependExtensionInterface
{
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

        if (!in_array($driver, SyliusCoreBundle::getSupportedDrivers())) {
            throw new \InvalidArgumentException(sprintf('Driver "%s" is unsupported for SyliusCoreBundle', $driver));
        }

        $loader->load(sprintf('driver/%s.xml', $driver));

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
        $configs = $container->getExtensionConfig($this->getAlias());
        $config  = $this->processConfiguration(new Configuration(), $configs);

        if (isset($config['driver'])) {
            $config = array('driver' => $config['driver']);
            foreach ($container->getExtensions() as $name => $extension) {
                switch ($name) {
                    case 'sylius_addressing':
                    case 'sylius_inventory':
                    case 'sylius_money':
                    case 'sylius_payments':
                    case 'sylius_product':
                    case 'sylius_promotions':
                    case 'sylius_sales':
                    case 'sylius_settings':
                    case 'sylius_shipping':
                    case 'sylius_taxation':
                    case 'sylius_taxonomies':
                        $container->prependExtensionConfig($name, $config);
                        break;
                }
            }
        }
    }

    /**
     * Remap class parameters.
     *
     * @param array            $classes
     * @param ContainerBuilder $container
     */
    protected function mapClassParameters(array $classes, ContainerBuilder $container)
    {
        foreach ($classes as $model => $serviceClasses) {
            foreach ($serviceClasses as $service => $class) {
                $service = $service === 'form' ? 'form.type' : $service;
                $container->setParameter(sprintf('sylius.%s.%s.class', $service, $model), $class);
            }
        }
    }
}
