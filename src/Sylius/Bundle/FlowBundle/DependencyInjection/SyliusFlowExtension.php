<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Flows extension.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusFlowExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/container'));

        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $config);

        $container->setAlias('sylius.process_storage', $config['storage']);

        $configurations = array(
            'builders',
            'validators',
            'contexts',
            'controllers',
            'coordinators',
            'storages',
        );

        foreach ($configurations as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }
    }
}
