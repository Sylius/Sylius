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

use Sylius\Bundle\ApiBundle\Serializer\Normalizer\HydraErrorNormalizer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class LegacyErrorHandlingCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('sylius_api.legacy_error_handling')) {
            return;
        }

        $legacyErrorHandling = $container->getParameter('sylius_api.legacy_error_handling');

        if (true !== $legacyErrorHandling) {
            return;
        }

        if ($container->hasDefinition(HydraErrorNormalizer::class)) {
            $container->removeDefinition(HydraErrorNormalizer::class);
        }
    }
}
