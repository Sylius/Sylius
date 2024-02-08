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

namespace Sylius\Bundle\UiBundle\Tests\Functional;

use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Twig\Environment;

final class TemplateEventTest extends KernelTestCase
{
    private ?object $twig = null;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->twig = self::getContainer()->get(Environment::class);
    }

    /** @test */
    public function it_renders_template_events_blocks(): void
    {
        // See Kernel.php for the configuration resulting in those lines
        $expectedLines = [
            'First block',
            'Second block',
            'Third block',
            'The king is dead, long live the king!',
        ];
        $renderedLines = array_values(array_filter(explode("\n", $this->twig->render('templateEvents.txt.twig'))));

        Assert::assertSame($expectedLines, $renderedLines);
    }

    /** @test */
    public function it_renders_debug_info_in_html_comments_while_rendering_in_test_environment(): void
    {
        // See Kernel.php for the configuration resulting in those lines
        $expectedLines = [
            '<!-- BEGIN EVENT | event name: "event" -->',
            '<!-- BEGIN BLOCK | event name: "event", block name: "first", template: "blocks/html/first.html.twig", priority: 5 -->',
            '<p id="first">First block</p>',
            '<!-- END BLOCK | event name: "event", block name: "first" -->',
            '<!-- BEGIN BLOCK | event name: "event", block name: "context", template: "blocks/html/context.html.twig", priority: -5 -->',
            '<p class="context">The king is dead, long live the king!</p>',
            '<!-- END BLOCK | event name: "event", block name: "context" -->',
            '<!-- END EVENT | event name: "event" -->',
        ];
        $renderedLines = array_values(array_filter(explode("\n", $this->twig->render('templateEvents.html.twig'))));

        Assert::assertSame($expectedLines, $renderedLines);
    }

    /** @test */
    public function it_passes_context_defined_in_template_block_configuration_during_rendering(): void
    {
        // See Kernel.php for the configuration resulting in those lines
        $expectedLines = [
            'Block: option1=foo, option2=baz',
        ];
        $renderedLines = array_values(array_filter(explode("\n", $this->twig->render('contextTemplateBlock.txt.twig'))));

        Assert::assertSame($expectedLines, $renderedLines);
    }

    /** @test */
    public function it_passes_context_with_custom_context_provider_defined_in_block_configuration_during_rendering(): void
    {
        // See Kernel.php for the configuration resulting in those lines
        $expectedLines = [
            'Block: option1=foo, option2=baz, custom=yolo',
        ];
        $renderedLines = array_values(array_filter(explode("\n", $this->twig->render('customContextProvider.txt.twig'))));

        Assert::assertSame($expectedLines, $renderedLines);
    }

    /** @test */
    public function it_renders_multiple_events_at_once(): void
    {
        // See Kernel.php for the configuration resulting in those lines
        $expectedLines = [
            'Generic block #2 (value=42)',
            'Specific block',
            'Generic block #1',
        ];

        $renderedLines = array_values(array_filter(explode("\n", $this->twig->render('multipleEvents.txt.twig'))));

        Assert::assertSame($expectedLines, $renderedLines);
    }
}
