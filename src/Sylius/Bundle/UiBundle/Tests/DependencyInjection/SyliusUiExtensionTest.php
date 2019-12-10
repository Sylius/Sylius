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

namespace Sylius\Bundle\UiBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\UiBundle\DependencyInjection\SyliusUiExtension;

final class SyliusUiExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_configures_the_multiple_event_block_listener_service_with_events_and_blocks_data(): void
    {
        $this->load(['events' => [
            'first_event' => ['blocks' => [
                'first_block' => ['template' => 'first.html.twig', 'enabled' => true, 'priority' => 0],
                'second_block' => ['template' => 'second.html.twig', 'enabled' => true, 'priority' => 0],
            ]],
            'second_event' => ['blocks' => [
                'another_block' => ['template' => 'another.html.twig', 'enabled' => true, 'priority' => 0],
            ]],
        ]]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.ui.sonata_multiple_block_event_listener',
            0,
            [
                'first_event' => [
                    ['template' => 'first.html.twig', 'name' => 'first_block'],
                    ['template' => 'second.html.twig', 'name' => 'second_block'],
                ],
                'second_event' => [
                    ['template' => 'another.html.twig', 'name' => 'another_block'],
                ],
            ]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'sylius.ui.sonata_multiple_block_event_listener',
            'kernel.event_listener',
            ['event' => 'sonata.block.event.first_event', 'method' => '__invoke']
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'sylius.ui.sonata_multiple_block_event_listener',
            'kernel.event_listener',
            ['event' => 'sonata.block.event.second_event', 'method' => '__invoke']
        );
    }

    /** @test */
    public function it_does_not_register_disabled_blocks(): void
    {
        $this->load(['events' => [
            'event_name' => ['blocks' => [
                'first_block' => ['template' => 'first.html.twig', 'enabled' => false, 'priority' => 0],
                'second_block' => ['template' => 'second.html.twig', 'enabled' => true, 'priority' => 0],
            ]],
        ]]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.ui.sonata_multiple_block_event_listener',
            0,
            ['event_name' => [
                ['template' => 'second.html.twig', 'name' => 'second_block'],
            ]]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'sylius.ui.sonata_multiple_block_event_listener',
            'kernel.event_listener',
            ['event' => 'sonata.block.event.event_name', 'method' => '__invoke']
        );
    }

    /** @test */
    public function it_sorts_blocks_by_their_priority(): void
    {
        $this->load(['events' => [
            'event_name' => ['blocks' => [
                'third_block' => ['template' => 'third.html.twig', 'enabled' => true, 'priority' => -5],
                'fourth_block' => ['template' => 'fourth.html.twig', 'enabled' => true, 'priority' => -10],
                'second_block' => ['template' => 'second.html.twig', 'enabled' => true, 'priority' => 0],
                'first_block' => ['template' => 'first.html.twig', 'enabled' => true, 'priority' => 5],
            ]],
        ]]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.ui.sonata_multiple_block_event_listener',
            0,
            ['event_name' => [
                ['template' => 'first.html.twig', 'name' => 'first_block'],
                ['template' => 'second.html.twig', 'name' => 'second_block'],
                ['template' => 'third.html.twig', 'name' => 'third_block'],
                ['template' => 'fourth.html.twig', 'name' => 'fourth_block'],
            ]]
        );
    }

    /** @test */
    public function it_sorts_blocks_by_their_priority_and_uses_fifo_ordering(): void
    {
        $this->load(['events' => [
            'event_name' => ['blocks' => [
                'fourth_block' => ['template' => 'fourth.html.twig', 'enabled' => true, 'priority' => -5],
                'second_block' => ['template' => 'second.html.twig', 'enabled' => true, 'priority' => 0],
                'third_block' => ['template' => 'third.html.twig', 'enabled' => true, 'priority' => 0],
                'first_block' => ['template' => 'first.html.twig', 'enabled' => true, 'priority' => 5],
            ]],
        ]]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius.ui.sonata_multiple_block_event_listener',
            0,
            ['event_name' => [
                ['template' => 'first.html.twig', 'name' => 'first_block'],
                ['template' => 'second.html.twig', 'name' => 'second_block'],
                ['template' => 'third.html.twig', 'name' => 'third_block'],
                ['template' => 'fourth.html.twig', 'name' => 'fourth_block'],
            ]]
        );
    }

    /** @test */
    public function it_does_not_register_listener_for_event_that_has_all_blocks_disabled(): void
    {
        $this->load(['events' => [
            'event_name' => ['blocks' => [
                'first_block' => ['template' => 'first.html.twig', 'enabled' => false, 'priority' => 0],
                'second_block' => ['template' => 'second.html.twig', 'enabled' => false, 'priority' => 0],
            ]],
        ]]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sylius.ui.sonata_multiple_block_event_listener', 0, []);

        $listenerDefinition = $this->container->findDefinition('sylius.ui.sonata_multiple_block_event_listener');
        $this->assertEmpty($listenerDefinition->getTags());
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusUiExtension()];
    }
}
