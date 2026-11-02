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

namespace Sylius\Bundle\OrderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Contracts\Service\ResetInterface;

final class TagResettableCartContextsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $taggedServices = $container->findTaggedServiceIds(RegisterCartContextsPass::CART_CONTEXT_SERVICE_TAG);

        foreach ($taggedServices as $id => $tags) {
            $definition = $container->getDefinition($id);

            if ($definition->hasTag('kernel.reset')) {
                continue;
            }

            if (is_subclass_of($definition->getClass(), ResetInterface::class)) {
                $definition->addTag('kernel.reset', ['method' => 'reset']);
            }
        }
    }
}
