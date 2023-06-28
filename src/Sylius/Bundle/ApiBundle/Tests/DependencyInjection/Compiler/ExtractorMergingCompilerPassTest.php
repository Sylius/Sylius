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

namespace DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Merger\LegacyResourceMetadataMerger;
use Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\MergingXmlExtractor;
use Sylius\Bundle\ApiBundle\DependencyInjection\Compiler\ExtractorMergingCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class ExtractorMergingCompilerPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_does_nothing_when_container_has_no_xml_extractor(): void
    {
        $this->compile();

        $this->assertContainerBuilderNotHasService('api_platform.metadata.extractor.xml.legacy');
    }

    /** @test */
    public function it_overwrites_xml_extractor(): void
    {
        $this->setDefinition(
            'api_platform.metadata.extractor.xml.legacy',
            new Definition(null, [[], new Reference('service_container')]),
        );
        $this->setDefinition(LegacyResourceMetadataMerger::class, new Definition());

        $this->compile();

        $this->assertContainerBuilderHasService(
            'api_platform.metadata.extractor.xml.legacy',
            MergingXmlExtractor::class,
        );
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'api_platform.metadata.extractor.xml.legacy',
            2,
            new Reference(LegacyResourceMetadataMerger::class),
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ExtractorMergingCompilerPass());
    }
}
