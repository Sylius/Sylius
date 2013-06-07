<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\DependencyInjection;

use Sylius\Bundle\AddressingBundle\SyliusAddressingBundle;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Addressing system extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusAddressingExtension extends Extension
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

        if (!in_array($driver, SyliusAddressingBundle::getSupportedDrivers())) {
            throw new \InvalidArgumentException(sprintf('Driver "%s" is unsupported for SyliusAddressingBundle', $driver));
        }

        $loader->load(sprintf('driver/%s.xml', $driver));

        $container->setParameter('sylius.driver', $driver);
        $container->setParameter('sylius.engine', $config['engine']);

        $this->mapClassParameters($config['classes'], $container);
        $this->mapValidationGroupParameters($config['validation_groups'], $container);

        $loader->load('services.xml');
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

    /**
     * Remap validation group parameters.
     *
     * @param array            $classes
     * @param ContainerBuilder $container
     */
    protected function mapValidationGroupParameters(array $validationGroups, ContainerBuilder $container)
    {
        foreach ($validationGroups as $model => $groups) {
            $container->setParameter(sprintf('sylius.validation_group.%s', $model), $groups);
        }
    }
}
