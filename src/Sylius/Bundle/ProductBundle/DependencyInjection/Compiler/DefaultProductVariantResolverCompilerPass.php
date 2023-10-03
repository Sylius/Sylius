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

namespace Sylius\Bundle\ProductBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class DefaultProductVariantResolverCompilerPass implements CompilerPassInterface
{
    private const DEFAULT_PRIORITY = -999;

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('sylius.product_variant_resolver.default')) {
            return;
        }

        // In case someone overwritten the service, we need to make sure it's tagged
        $defaultResolver = $container->getDefinition('sylius.product_variant_resolver.default');
        if (!$defaultResolver->hasTag('sylius.product_variant_resolver')) {
            $defaultResolver->addTag('sylius.product_variant_resolver', ['priority' => self::DEFAULT_PRIORITY]);
        }
    }
}
