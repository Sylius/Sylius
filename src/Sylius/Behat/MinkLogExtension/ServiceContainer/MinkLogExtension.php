<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\MinkLogExtension\ServiceContainer;

use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Sylius\Behat\MinkLogExtension\Listener\FailedStepListener;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MinkLogExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $this->loadStepFailureListener($container);

        $container->setParameter('log.directory', $config['directory']);
        $container->setParameter('log.screenshot', $config['screenshot']);
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->scalarNode('directory')->defaultNull()->end()
                ->booleanNode('screenshot')->defaultFalse()->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'log';
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
    public function process(ContainerBuilder $container)
    {
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadStepFailureListener(ContainerBuilder $container)
    {
        $definition = new Definition(FailedStepListener::class, [
            new Reference('mink'),
            '%log.directory%',
            '%log.screenshot%',
        ]);

        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, ['priority' => 0]);

        $container->setDefinition('log.listener.step_failure', $definition);
    }
}