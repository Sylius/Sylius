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
use Sylius\Bundle\ApiBundle\DependencyInjection\Compiler\CommandDataTransformerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class CommandDataTransformerPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_collects_tagged_command_data_transformer_services(): void
    {
        $this->setDefinition(
            'sylius.api.command_data_transformer.service.first',
            (new Definition())->addTag('sylius.api.command_data_transformer'),
        );

        $this->setDefinition(
            'sylius.api.command_data_transformer.service.second',
            (new Definition())->addTag('sylius.api.command_data_transformer'),
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.api.data_transformer.command_aware_input_data_transformer',
            0,
            'sylius.api.command_data_transformer.service.first',
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.api.data_transformer.command_aware_input_data_transformer',
            1,
            'sylius.api.command_data_transformer.service.second',
        );
    }

    /** @test */
    public function it_collects_tagged_command_data_transformer_services_with_priorities(): void
    {
        $this->setDefinition(
            'sylius.api.command_data_transformer.service.first',
            (new Definition())->addTag('sylius.api.command_data_transformer', ['priority' => 20]),
        );

        $this->setDefinition(
            'sylius.api.command_data_transformer.service.second',
            (new Definition())->addTag('sylius.api.command_data_transformer', ['priority' => 10]),
        );

        $this->setDefinition(
            'sylius.api.command_data_transformer.service.third',
            (new Definition())->addTag('sylius.api.command_data_transformer', ['priority' => 30]),
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.api.data_transformer.command_aware_input_data_transformer',
            0,
            'sylius.api.command_data_transformer.service.third',
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.api.data_transformer.command_aware_input_data_transformer',
            1,
            'sylius.api.command_data_transformer.service.first',
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.api.data_transformer.command_aware_input_data_transformer',
            2,
            'sylius.api.command_data_transformer.service.second',
        );
    }

    /**
     * @test
     */
    public function it_creates_definition_without_any_transformers(): void
    {
        $this->compile();

        $this->assertContainerBuilderHasService('sylius.api.data_transformer.command_aware_input_data_transformer');
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CommandDataTransformerPass());
    }
}
