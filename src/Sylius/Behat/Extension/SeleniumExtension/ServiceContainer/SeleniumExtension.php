<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Extension\SeleniumExtension\ServiceContainer;

use Behat\MinkExtension\ServiceContainer\MinkExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Sylius\Behat\Extension\SeleniumExtension\SeleniumSessionRestarter;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SeleniumExtension implements Extension
{
    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'sylius_selenium';
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
        $definition = new Definition(SeleniumSessionRestarter::class, [new Reference(MinkExtension::MINK_ID)]);
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG);

        $container->setDefinition('sylius_selenium.selenium_session_restarter', $definition);
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
    }
}
