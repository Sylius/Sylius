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
            'sylius.twig.extension.template_event',
            1,
            [
                'first_event' => [
                    'first.html.twig',
                    'second.html.twig',
                ],
                'second_event' => [
                    'another.html.twig',
                ],
            ]
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
            'sylius.twig.extension.template_event',
            1,
            ['event_name' => [
                'second.html.twig',
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
            'sylius.twig.extension.template_event',
            1,
            ['event_name' => [
                'first.html.twig',
                'second.html.twig',
                'third.html.twig',
                'fourth.html.twig',
            ]]
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusUiExtension()];
    }
}
