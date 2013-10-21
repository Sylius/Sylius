<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\SyliusResourceExtension;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Settings system extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusSettingsExtension extends SyliusResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $processor = new Processor();

        $config = $processor->processConfiguration(new Configuration(), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $driver = $config['driver'];

        $this->loadDatabaseDriver($driver, $loader);

        $container->setParameter('sylius_settings.driver', $driver);
        $container->setParameter('sylius_settings.driver.'.$driver, true);

        $classes = $config['classes'];
        $parameterClasses = $classes['parameter'];

        if (isset($parameterClasses['model'])) {
            $container->setParameter('sylius.model.parameter.class', $parameterClasses['model']);
        }

        if (isset($parameterClasses['repository'])) {
            $container->setParameter('sylius.repository.parameter.class', $parameterClasses['repository']);
        }

        if ($container->hasParameter('sylius.config.classes')) {
            $classes = array_merge($classes, $container->getParameter('sylius.config.classes'));
        }

        $container->setParameter('sylius.config.classes', $classes);

        $loader->load('services.xml');
    }
}
