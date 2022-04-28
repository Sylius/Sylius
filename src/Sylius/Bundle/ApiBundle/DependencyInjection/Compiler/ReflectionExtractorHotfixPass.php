<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\DependencyInjection\Compiler;

use Sylius\Bundle\ApiBundle\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\PropertyInfo\PropertyReadInfoExtractorInterface;

/**
 * @internal
 *
 * @see ReflectionExtractor
 */
final class ReflectionExtractorHotfixPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @psalm-suppress UndefinedClass */
        if (!interface_exists(PropertyReadInfoExtractorInterface::class)) {
            // This class was introduced in Symfony 5.1, same Symfony version that introduced the BC break.
            return;
        }

        try {
            /** @psalm-suppress MissingDependency */
            $container->findDefinition('property_info.reflection_extractor')->setClass(ReflectionExtractor::class);
        } catch (ServiceNotFoundException $exception) {
            return;
        }
    }
}
