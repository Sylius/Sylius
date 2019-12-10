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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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

        $this->loadEvents($config['events'], $container);
    }

    /**
     * @psalm-param array<string, array{blocks: array<string, array{template: string, priority: int, enabled: bool}>}> $eventsConfig
     */
    private function loadEvents(array $eventsConfig, ContainerBuilder $container): void
    {
        $multipleBlockEventListenerDefinition = $container->getDefinition('sylius.ui.sonata_multiple_block_event_listener');

        $blocksForEvents = [];
        foreach ($eventsConfig as $eventName => $eventConfiguration) {
            $blocksPriorityQueue = new SplPriorityQueue();

            foreach ($eventConfiguration['blocks'] as $blockName => $details) {
                if ($details['enabled'] === false) {
                    continue;
                }

                $blocksPriorityQueue->insert(
                    [
                        'template' => $details['template'],
                        'name' => $blockName,
                    ],
                    $details['priority']
                );
            }

            if ($blocksPriorityQueue->count() === 0) {
                continue;
            }

            $blocksForEvents[$eventName] = $blocksPriorityQueue->toArray();

            $multipleBlockEventListenerDefinition->addTag(
                'kernel.event_listener',
                [
                    'event' => sprintf('sonata.block.event.%s', $eventName),
                    'method' => '__invoke',
                ]
            );
        }

        $multipleBlockEventListenerDefinition->setArgument(0, $blocksForEvents);
    }
}
