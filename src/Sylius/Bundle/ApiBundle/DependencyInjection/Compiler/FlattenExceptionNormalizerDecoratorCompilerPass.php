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

namespace Sylius\Bundle\ApiBundle\DependencyInjection\Compiler;

use Sylius\Bundle\ApiBundle\Serializer\FlattenExceptionNormalizer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class FlattenExceptionNormalizerDecoratorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('fos_rest.serializer.flatten_exception_normalizer')) {
            $container->removeDefinition(FlattenExceptionNormalizer::class);
        }
    }
}
