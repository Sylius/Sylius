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

namespace Sylius\Bundle\ApiBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\ApiBundle\DependencyInjection\Compiler\LegacyErrorHandlingCompilerPass;
use Sylius\Bundle\ApiBundle\Serializer\FlattenExceptionNormalizer;
use Sylius\Bundle\ApiBundle\Serializer\HydraErrorNormalizer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class LegacyErrorHandlingCompilerPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_does_nothing_when_legacy_error_handling_is_disabled(): void
    {
        $this->setParameter('sylius_api.legacy_error_handling', false);
        $this->setDefinition(HydraErrorNormalizer::class, new Definition());
        $this->setDefinition(FlattenExceptionNormalizer::class, new Definition());

        $this->compile();

        $this->assertContainerBuilderHasService(HydraErrorNormalizer::class);
        $this->assertContainerBuilderHasService(FlattenExceptionNormalizer::class);
    }

    /** @test */
    public function it_removes_normalizers_decorators_when_legacy_error_handling_is_enabled(): void
    {
        $this->setParameter('sylius_api.legacy_error_handling', true);
        $this->setDefinition(HydraErrorNormalizer::class, new Definition());
        $this->setDefinition(FlattenExceptionNormalizer::class, new Definition());

        $this->compile();

        $this->assertContainerBuilderNotHasService(HydraErrorNormalizer::class);
        $this->assertContainerBuilderNotHasService(FlattenExceptionNormalizer::class);
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new LegacyErrorHandlingCompilerPass());
    }
}
