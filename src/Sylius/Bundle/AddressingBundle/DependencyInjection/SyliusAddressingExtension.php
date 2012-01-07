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

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;

/**
 * Addressing system extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusAddressingExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $config);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/container'));

        if (!in_array($config['driver'], array('ORM'))) {
            throw new \InvalidArgumentException(sprintf('Driver "%s" is unsupported for this extension.', $config['driver']));
        }

        if (!in_array($config['engine'], array('php', 'twig'))) {
            throw new \InvalidArgumentException(sprintf('Engine "%s" is unsupported for this extension.', $config['engine']));
        }

        $loader->load(sprintf('driver/%s.xml', $config['driver']));
        $loader->load(sprintf('engine/%s.xml', $config['engine']));

        $container->setParameter('sylius_addressing.driver', $config['driver']);
        $container->setParameter('sylius_addressing.engine', $config['engine']);

        $configurations = array(
            'controllers',
            'manipulators',
            'forms',
        );

        foreach($configurations as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        $this->remapParametersNamespaces($config['classes'], $container, array(
            'model'       => 'sylius_addressing.model.%s.class',
            'manipulator' => 'sylius_addressing.manipulator.%s.class'
        ));

        $this->remapParametersNamespaces($config['classes']['form'], $container, array(
            'type' => 'sylius_addressing.form.type.%s.class',
        ));

        $this->remapParametersNamespaces($config['classes']['controller'], $container, array(
            'backend'  => 'sylius_addressing.controller.backend.%s.class',
            'frontend' => 'sylius_addressing.controller.frontend.%s.class'
        ));
    }

  	/**
     * Remap parameters.
     */
    protected function remapParameters(array $config, ContainerBuilder $container, array $map)
    {
        foreach ($map as $name => $paramName) {
            if (isset($config[$name])) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }

    /**
     * Remap parameter namespaces.
     */
    protected function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces)
    {
        foreach ($namespaces as $ns => $map) {
            if ($ns) {
                if (!isset($config[$ns])) {
                    continue;
                }
                $namespaceConfig = $config[$ns];
            } else {
                $namespaceConfig = $config;
            }
            if (is_array($map)) {
                $this->remapParameters($namespaceConfig, $container, $map);
            } else {
                foreach ($namespaceConfig as $name => $value) {
                    if (null !== $value) {
                        $container->setParameter(sprintf($map, $name), $value);
                    }
                }
            }
        }
    }
}
