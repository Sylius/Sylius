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

namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterUriBasedSectionResolverPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('sylius.section_resolver.uri_based_section_resolver')) {
            return;
        }

        $uriBasedSectionResolver = $container->getDefinition('sylius.section_resolver.uri_based_section_resolver');
        $uriBasedSectionProviders = [];

        foreach ($container->findTaggedServiceIds('sylius.uri_based_section_resolver') as $id => $tags) {
            foreach ($tags as $attributes) {
                $uriBasedSectionProviders[] = ['id' => new Reference($id), 'priority' => $attributes['priority'] ?? 0];
            }
        }

        usort($uriBasedSectionProviders, static fn (array $a, array $b): int => -($a['priority'] <=> $b['priority']));

        $uriBasedSectionResolver->setArgument(1, array_column($uriBasedSectionProviders, 'id'));
    }
}
