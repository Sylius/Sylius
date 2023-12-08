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

namespace Sylius\Bundle\ApiBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\ApiBundle\DependencyInjection\Compiler\FlattenExceptionNormalizerDecoratorCompilerPass;
use Sylius\Bundle\ApiBundle\Serializer\FlattenExceptionNormalizer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class FlattenExceptionNormalizerDecoratorCompilerPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_doesnt_remove_decoration_when_decorated_service_exists(): void
    {
        $this->setDefinition(FlattenExceptionNormalizer::class, new Definition());
        $this->setDefinition('fos_rest.serializer.flatten_exception_normalizer', new Definition());

        $this->compile();

        $this->assertContainerBuilderHasService(FlattenExceptionNormalizer::class);
    }

    /** @test */
    public function it_removes_decoration_when_decorated_service_doesnt_exist(): void
    {
        $this->setDefinition(FlattenExceptionNormalizer::class, new Definition());

        $this->compile();

        $this->assertContainerBuilderNotHasService(FlattenExceptionNormalizer::class);
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new FlattenExceptionNormalizerDecoratorCompilerPass());
    }
}
