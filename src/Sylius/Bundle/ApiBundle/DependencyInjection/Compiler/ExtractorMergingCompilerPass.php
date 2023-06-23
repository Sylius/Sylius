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

use Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Merger\LegacyResourceMetadataMerger;
use Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\MergingXmlExtractor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ExtractorMergingCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition('api_platform.metadata.extractor.xml.legacy')) {
            $definition = $container->getDefinition('api_platform.metadata.extractor.xml.legacy');
            $definition->setClass(MergingXmlExtractor::class);
            $definition->addArgument(new Reference(LegacyResourceMetadataMerger::class));
        }
    }
}
