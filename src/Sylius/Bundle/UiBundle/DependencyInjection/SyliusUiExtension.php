<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UiBundle\DependencyInjection;

use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistryInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Zend\Stdlib\SplPriorityQueue;

final class SyliusUiExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');

        if ($container->getParameter('kernel.debug')) {
            $loader->load('services/debug/template_event.xml');
        }

        $this->loadEvents($config['events'], $container);
    }

    /**
     * @psalm-param array<string, array{blocks: array<string, array{template: string, priority: int, enabled: bool}>}> $eventsConfig
     */
    private function loadEvents(array $eventsConfig, ContainerBuilder $container): void
    {
        $templateBlockRegistryDefinition = $container->findDefinition(TemplateBlockRegistryInterface::class);

        $blocksForEvents = [];
        foreach ($eventsConfig as $eventName => $eventConfiguration) {
            $blocksPriorityQueue = new SplPriorityQueue();

            foreach ($eventConfiguration['blocks'] as $blockName => $details) {
                $blocksPriorityQueue->insert(
                    new Definition(TemplateBlock::class, [$blockName, $details['template'], $details['priority'], $details['enabled']]),
                    $details['priority']
                );
            }

            $blocksForEvents[$eventName] = $blocksPriorityQueue->toArray();
        }

        $templateBlockRegistryDefinition->setArgument(0, $blocksForEvents);
    }
}
