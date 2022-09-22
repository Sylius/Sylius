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

use Laminas\Stdlib\SplPriorityQueue;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistryInterface;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class SyliusUiExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');

        if ($container->getParameter('kernel.debug')) {
            $loader->load('services/debug/template_event.xml');
        }

        $this->loadEvents($config['events'], $container);
    }

    /**
     * @experimental
     *
     * @psalm-param array<string, array{blocks: array<string, array{template: string, context: array, priority: int, enabled: bool}>}> $eventsConfig
     */
    private function loadEvents(array $eventsConfig, ContainerBuilder $container): void
    {
        $templateBlockRegistryDefinition = $container->findDefinition(TemplateBlockRegistryInterface::class);

        $blocksForEvents = [];
        foreach ($eventsConfig as $eventName => $eventConfiguration) {
            $blocksPriorityQueue = new SplPriorityQueue();

            foreach ($eventConfiguration['blocks'] as $blockName => $details) {
                $details['name'] = $blockName;
                $details['eventName'] = $eventName;

                $blocksPriorityQueue->insert($details, $details['priority'] ?? 0);
            }

            foreach ($blocksPriorityQueue->toArray() as $details) {
                /** @psalm-var array{name: string, eventName: string, template: string, context: array, priority: int, enabled: bool} $details */
                $blocksForEvents[$eventName][$details['name']] = new Definition(TemplateBlock::class, [
                    $details['name'],
                    $details['eventName'],
                    $details['template'],
                    $details['context'],
                    $details['priority'],
                    $details['enabled'],
                ]);
            }
        }

        $templateBlockRegistryDefinition->setArgument(0, $blocksForEvents);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $useWebpack = $this->getCurrentConfiguration($container)['use_webpack'] ?? true;

        $container->setParameter('sylius_ui.use_webpack', $useWebpack);

        if (true === $useWebpack) {
            $container->prependExtensionConfig('framework', [
                'assets' => [
                    'packages' => [
                        'shop' => [
                            'json_manifest_path' => '%kernel.project_dir%/public/build/shop/manifest.json',
                        ],
                        'admin' => [
                            'json_manifest_path' => '%kernel.project_dir%/public/build/admin/manifest.json',
                        ],
                    ]
                ]
            ]);
        }
    }

    private function getCurrentConfiguration(ContainerBuilder $container): array
    {
        /** @var ConfigurationInterface $configuration */
        $configuration = $this->getConfiguration([], $container);

        $configs = $container->getExtensionConfig($this->getAlias());

        return $this->processConfiguration($configuration, $configs);
    }
}
