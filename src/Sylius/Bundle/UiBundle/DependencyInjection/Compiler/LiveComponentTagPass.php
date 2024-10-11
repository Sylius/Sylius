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

namespace Sylius\Bundle\UiBundle\DependencyInjection\Compiler;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LiveComponentTagPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $liveComponentTags = $container->getParameter('sylius_ui.twig_ux.live_component_tags') ?? [];
        $defaultTemplate = $container->getParameter('sylius_ui.twig_ux.component_default_template');

        foreach ($liveComponentTags as $name => $tagOptions) {
            foreach ($container->findTaggedServiceIds(sprintf('sylius.live_component.%s', $name)) as $id => $tags) {
                foreach ($tags as $tag) {
                    $liveComponentService = $container->getDefinition($id);
                    $liveComponentService->addTag('twig.component', [
                        'key' => $tag['key'] ?? $tagOptions['key'] ?? throw new InvalidArgumentException('The "key" attribute is required for the "sylius.live_component" tag'),
                        'template' => $tag['template'] ?? $tagOptions['template'] ?? $defaultTemplate,
                        'expose_public_props' => $tag['expose_public_props'] ?? $tagOptions['expose_public_props'] ?? true,
                        'attributes_var' => $tag['attributes_var'] ?? $tagOptions['attributes_var'] ?? 'attributes',
                        'default_action' => $tag['default_action'] ?? $tagOptions['default_action'] ?? null,
                        'live' => true,
                        'csrf' => $tag['csrf'] ?? $tagOptions['csrf'] ?? true,
                        'route' => $tag['route'] ?? $tagOptions['route'],
                        'method' => $tag['method'] ?? $tagOptions['method'] ?? 'post',
                        'url_reference_type' => $tag['url_reference_type'] ?? $tagOptions['url_reference_type'] ?? UrlGeneratorInterface::ABSOLUTE_PATH,
                    ]);
                    $liveComponentService->addTag('controller.service_arguments');
                }
            }
        }
    }
}
