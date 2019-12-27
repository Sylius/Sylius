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
                'first_block' => ['template' => 'first.html.twig', 'enabled' => true, 'priority' => 0],
                'second_block' => ['template' => 'second.html.twig', 'enabled' => true, 'priority' => 0],
            ]],
            'second_event' => ['blocks' => [
                'another_block' => ['template' => 'another.html.twig', 'enabled' => true, 'priority' => 0],
            ]],
        ]]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            TemplateBlockRegistryInterface::class,
            0,
            [
                'first_event' => [
                    new Definition(TemplateBlock::class, ['first_block', 'first.html.twig', 0, true]),
                    new Definition(TemplateBlock::class, ['second_block', 'second.html.twig', 0, true]),
                ],
                'second_event' => [
                    new Definition(TemplateBlock::class, ['another_block', 'another.html.twig', 0, true]),
                ],
            ]
        );
    }

    /** @test */
    public function it_sorts_blocks_by_their_priority_and_uses_fifo_ordering(): void
    {
        $this->container->setParameter('kernel.debug', true);

        $this->load(['events' => [
            'event_name' => ['blocks' => [
                'fourth_block' => ['template' => 'fourth.html.twig', 'enabled' => true, 'priority' => -5],
                'second_block' => ['template' => 'second.html.twig', 'enabled' => true, 'priority' => 0],
                'third_block' => ['template' => 'third.html.twig', 'enabled' => true, 'priority' => 0],
                'first_block' => ['template' => 'first.html.twig', 'enabled' => true, 'priority' => 5],
            ]],
        ]]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            TemplateBlockRegistryInterface::class,
            0,
            ['event_name' => [
                new Definition(TemplateBlock::class, ['first_block', 'first.html.twig', 5, true]),
                new Definition(TemplateBlock::class, ['second_block', 'second.html.twig', 0, true]),
                new Definition(TemplateBlock::class, ['third_block', 'third.html.twig', 0, true]),
                new Definition(TemplateBlock::class, ['fourth_block', 'fourth.html.twig', -5, true]),
            ]]
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusUiExtension()];
    }
}
