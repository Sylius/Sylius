<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class SyliusUiExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.php');

        $container->setParameter('sylius_ui.twig_ux.anonymous_component_template_prefixes', $config['twig_ux']['anonymous_component_template_prefixes'] ?? []);
        $container->setParameter('sylius_ui.twig_ux.live_component_tags', $config['twig_ux']['live_component_tags'] ?? []);
        $container->setParameter('sylius_ui.twig_ux.component_default_template', $config['twig_ux']['component_default_template']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('ux_icons', $this->getUxIconsConfiguration());
    }

    /** @return array<string, mixed> */
    private function getUxIconsConfiguration(): array
    {
        return [
            'icon_sets' => [
                'tabler' => [
                    'path' => __DIR__ . '/../Resources/assets/icons/tabler',
                ],
                'flagpack' => [
                    'path' => __DIR__ . '/../Resources/assets/icons/flagpack',
                ],
            ],
            'iconify' => [
                'enabled' => true,
            ],
            'default_icon_attributes' => [
                'class' => 'icon',
            ],
            'ignore_not_found' => true,
        ];
    }
}
