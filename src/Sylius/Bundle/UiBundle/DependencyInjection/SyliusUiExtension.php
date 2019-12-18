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
        $templateEventExtensionDefinition = $container->getDefinition('sylius.twig.extension.template_event');

        $blocksForEvents = [];
        foreach ($eventsConfig as $eventName => $eventConfiguration) {
            $blocksPriorityQueue = new SplPriorityQueue();

            foreach ($eventConfiguration['blocks'] as $blockName => $details) {
                if (!$details['enabled']) {
                    continue;
                }

                $blocksPriorityQueue->insert($details['template'], $details['priority']);
            }

            if ($blocksPriorityQueue->count() === 0) {
                continue;
            }

            $blocksForEvents[$eventName] = $blocksPriorityQueue->toArray();
        }

        $templateEventExtensionDefinition->setArgument(1, $blocksForEvents);
    }
}
