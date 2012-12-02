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

        if (!in_array($config['driver'], SyliusAddressingBundle::getSupportedDrivers())) {
            throw new \InvalidArgumentException(sprintf('Driver "%s" is unsupported for this extension.', $config['driver']));
        }

        $loader->load(sprintf('driver/%s.xml', $config['driver']));

        $container->setParameter('sylius_addressing.driver', $config['driver']);
        $container->setParameter('sylius_addressing.engine', $config['engine']);

        $container->setParameter('sylius_addressing.model.address.class', $config['classes']['model']['address']);
        $container->setParameter('sylius_addressing.controller.address.class', $config['classes']['controller']['address']);
        $container->setParameter('sylius_addressing.form.type.address.class', $config['classes']['form']['type']['address']);

        $loader->load('services.xml');
    }
}
