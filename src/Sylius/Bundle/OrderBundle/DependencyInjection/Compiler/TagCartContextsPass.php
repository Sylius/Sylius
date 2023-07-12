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

use Sylius\Component\Order\Context\ResettingCartContextInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class TagCartContextsPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container) : void
    {
        $taggedServices = $container->findTaggedServiceIds(RegisterCartContextsPass::CART_CONTEXT_SERVICE_TAG);

        foreach ($taggedServices as $id => $tags) {
            $definition = $container->getDefinition($id);

            if(is_subclass_of($definition->getClass(), ResettingCartContextInterface::class)) {
                $definition->addTag('kernel.reset', ['method' => 'reset']);
            }
        }
    }
}
