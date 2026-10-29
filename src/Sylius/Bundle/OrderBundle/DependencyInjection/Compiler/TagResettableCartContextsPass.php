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
        $ids = array_keys($container->findTaggedServiceIds(RegisterCartContextsPass::CART_CONTEXT_SERVICE_TAG));

        foreach ($this->decoratorsOf($container, $ids) as $id) {
            $ids[] = $id;
        }

        foreach ($ids as $id) {
            $definition = $container->getDefinition($id);

            if ($definition->hasTag('kernel.reset')) {
                continue;
            }

            if (is_subclass_of($definition->getClass(), ResetInterface::class)) {
                $definition->addTag('kernel.reset', ['method' => 'reset']);
            }
        }
    }

    /**
     * A decorator does not carry the decorated service's tags yet: they are merged
     * by DecoratorServicePass, which runs in the optimization phase - after
     * ResettableServicePass (BEFORE_OPTIMIZATION, -32) has already built
     * services_resetter. Any pass that drives behaviour off a tag therefore has to
     * resolve decorators itself, or it silently skips them.
     *
     * @param list<string> $ids
     *
     * @return list<string>
     */
    private function decoratorsOf(ContainerBuilder $container, array $ids): array
    {
        $found = [];
        $known = $ids;

        do {
            $added = [];

            foreach ($container->getDefinitions() as $id => $definition) {
                $decorated = $definition->getDecoratedService();

                if (null === $decorated || \in_array($id, $known, true)) {
                    continue;
                }

                if (\in_array($decorated[0], $known, true)) {
                    $added[] = $id;
                }
            }

            $found = [...$found, ...$added];
            $known = [...$known, ...$added];
        } while ([] !== $added);

        return $found;
    }
}
