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

use Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Extractor\XmlResourceExtractor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class XmlResourceExtractorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition('api_platform.metadata.resource_extractor.xml')) {
            $definition = $container->getDefinition('api_platform.metadata.resource_extractor.xml');
            $definition->setClass(XmlResourceExtractor::class);
        }
    }
}
