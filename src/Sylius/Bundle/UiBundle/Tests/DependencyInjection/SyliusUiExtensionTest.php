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

namespace Sylius\Bundle\UiBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\UiBundle\DependencyInjection\SyliusUiExtension;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistryInterface;
use Symfony\Component\DependencyInjection\Definition;

final class SyliusUiExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_configures_the_multiple_event_block_listener_service_with_events_and_blocks_data(): void
    {
        $this->container->setParameter('kernel.debug', true);

        $this->load(['events' => [
            'first_event' => ['blocks' => [
                'first_block' => ['template' => 'first.html.twig', 'context' => [], 'enabled' => true, 'priority' => 0],
                'second_block' => ['template' => 'second.html.twig', 'context' => ['foo' => 'bar'], 'enabled' => true, 'priority' => 0],
            ]],
            'second_event' => ['blocks' => [
                'another_block' => ['template' => 'another.html.twig', 'context' => [], 'enabled' => true, 'priority' => 0],
            ]],
        ]]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            TemplateBlockRegistryInterface::class,
            0,
            [
                'first_event' => [
                    'first_block' => new Definition(TemplateBlock::class, ['first_block', 'first_event', 'first.html.twig', [], 0, true]),
                    'second_block' => new Definition(TemplateBlock::class, ['second_block', 'first_event', 'second.html.twig', ['foo' => 'bar'], 0, true]),
                ],
                'second_event' => [
                    'another_block' => new Definition(TemplateBlock::class, ['another_block', 'second_event', 'another.html.twig', [], 0, true]),
                ],
            ],
        );
    }

    /** @test */
    public function it_sorts_blocks_by_their_priority_and_uses_fifo_ordering(): void
    {
        $this->container->setParameter('kernel.debug', true);

        $this->load(['events' => [
            'event_name' => ['blocks' => [
                'fourth_block' => ['template' => 'fourth.html.twig', 'context' => [], 'enabled' => true, 'priority' => -5],
                'second_block' => ['template' => 'second.html.twig', 'context' => [], 'enabled' => true, 'priority' => 0],
                'third_block' => ['template' => 'third.html.twig', 'context' => [], 'enabled' => true, 'priority' => 0],
                'first_block' => ['template' => 'first.html.twig', 'context' => [], 'enabled' => true, 'priority' => 5],
            ]],
        ]]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            TemplateBlockRegistryInterface::class,
            0,
            ['event_name' => [
                'first_block' => new Definition(TemplateBlock::class, ['first_block', 'event_name', 'first.html.twig', [], 5, true]),
                'second_block' => new Definition(TemplateBlock::class, ['second_block', 'event_name', 'second.html.twig', [], 0, true]),
                'third_block' => new Definition(TemplateBlock::class, ['third_block', 'event_name', 'third.html.twig', [], 0, true]),
                'fourth_block' => new Definition(TemplateBlock::class, ['fourth_block', 'event_name', 'fourth.html.twig', [], -5, true]),
            ]],
        );
    }

    /** @test */
    public function it_uses_webpack_when_parameter_is_not_defined(): void
    {
        $this->container->setParameter('kernel.debug', true);

        $this->load();

        $this->assertContainerBuilderHasParameter('sylius_ui.use_webpack', true);
    }

    /** @test */
    public function it_doesnt_use_webpack_when_parameter_is_set_to_false(): void
    {
        $this->container->setParameter('kernel.debug', true);
        $this->container->prependExtensionConfig('sylius_ui', ['use_webpack' => false]);

        $this->load();

        $this->assertContainerBuilderHasParameter('sylius_ui.use_webpack', false);
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusUiExtension()];
    }
}
