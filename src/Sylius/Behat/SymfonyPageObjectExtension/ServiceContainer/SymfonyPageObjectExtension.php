<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\SymfonyPageObjectExtension\ServiceContainer;

use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Sylius\Behat\SymfonyExtension\ServiceContainer\SymfonyExtension;
use Sylius\Behat\SymfonyPageObjectExtension\Factory\SymfonyPageObjectFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class SymfonyPageObjectExtension implements Extension
{
    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'sylius_symfony_page_object';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $container
            ->register('sylius.page_object.factory', SymfonyPageObjectFactory::class)
            ->setArguments([
                new Reference('sylius.page_object.factory.inner'),
                new Reference('mink'),
                new Reference('sensio_labs.page_object_extension.class_name_resolver'),
                '%sensio_labs.page_object_extension.page_factory.page_parameters%',
                $this->getSymfonyApplicationServiceDefinition('router'),
            ])
            ->setDecoratedService('sensio_labs.page_object_extension.page_factory')
            ->setPublic(false)
        ;
    }

    /**
     * @param string $serviceId
     *
     * @return Definition
     */
    private function getSymfonyApplicationServiceDefinition($serviceId)
    {
        return (new Definition(null, [$serviceId]))->setFactory([
            new Reference(SymfonyExtension::SHARED_KERNEL_CONTAINER_ID),
            'get',
        ]);
    }
}
