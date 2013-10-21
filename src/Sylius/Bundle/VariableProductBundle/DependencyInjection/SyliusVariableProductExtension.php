<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariableProductBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\SyliusResourceExtension;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Sylius product catalog system container extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusVariableProductExtension extends SyliusResourceExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/container'));

        $driver = $container->getParameter('sylius_product.driver');

        $this->loadDatabaseDriver($driver, $loader);

        $loader->load('options.xml');
        $loader->load('variants.xml');
        $loader->load('prototypes.xml');

        $classes = $config['classes'];

        $this->mapClassParameters($classes, $container);
        $this->mapValidationGroupParameters($config['validation_groups'], $container);

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
        if (!$container->hasExtension('sylius_product')) {
            return;
        }

        $container->prependExtensionConfig('sylius_product', array(
            'classes' => array(
                'product' => array(
                    'model' => 'Sylius\Bundle\VariableProductBundle\Model\VariableProduct',
                    'form'  => 'Sylius\Bundle\VariableProductBundle\Form\Type\VariableProductType'
                ),
                'prototype' => array(
                    'model' => 'Sylius\Bundle\VariableProductBundle\Model\Prototype',
                    'form'  => 'Sylius\Bundle\VariableProductBundle\Form\Type\PrototypeType'
                )
            ))
        );
    }
}
