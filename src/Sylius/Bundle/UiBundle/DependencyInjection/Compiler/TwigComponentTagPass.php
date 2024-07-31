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

final class TwigComponentTagPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('sylius.twig_component') as $id => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['key'], $tag['template'])) {
                    throw new InvalidArgumentException('The "key" and "template" attributes are required for the "sylius.twig_component" tag');
                }

                $twigComponentService = $container->getDefinition($id);
                $twigComponentService->addTag('twig.component', [
                    'key' => $tag['key'],
                    'template' => $tag['template'],
                    'expose_public_props' => $tag['expose_public_props'] ?? true,
                    'attributes_var' => $tag['attributes_var'] ?? 'attributes',
                ]);
            }
        }
    }
}
