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

final class TwigComponentTagPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('sylius.twig_component') as $id => $tags) {
            foreach ($tags as $tag) {
                $liveComponentService = $container->getDefinition($id);
                $liveComponentService->addTag('twig.component', [
                    'key' => $tag['key'] ?? throw new InvalidArgumentException('The "key" attribute is required for the "sylius.twig_component" tag'),
                    'template' => $tag['template'] ?? throw new InvalidArgumentException('The "template" attribute is required for the "sylius.twig_component" tag'),
                    'expose_public_props' => $tag['expose_public_props'] ?? true,
                    'attributes_var' => $tag['attributes_var'] ?? 'attributes',
                ]);
            }
        }
    }
}
