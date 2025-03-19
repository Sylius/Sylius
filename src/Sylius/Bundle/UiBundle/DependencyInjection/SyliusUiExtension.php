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
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class SyliusUiExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');

        $container->setParameter('sylius_ui.twig_ux.anonymous_component_template_prefixes', $config['twig_ux']['anonymous_component_template_prefixes'] ?? []);
        $container->setParameter('sylius_ui.twig_ux.live_component_tags', $config['twig_ux']['live_component_tags'] ?? []);
        $container->setParameter('sylius_ui.twig_ux.component_default_template', $config['twig_ux']['component_default_template']);
    }
}
