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

namespace Sylius\Bundle\UiBundle\Tests\Functional;

use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Twig\Environment;

final class TemplateEventTest extends KernelTestCase
{
    /** @var Environment */
    private $twig;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->twig = self::$container->get('twig');
    }

    /** @test */
    public function it_renders_template_events_blocks(): void
    {
        // See Kernel.php for the configuration resulting in those lines
        $expectedLines = [
            'First block (for event: "first_event")',
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
            '<!-- event name: "event", block name: "first", template: "blocks/html/first.html.twig", priority: 5 -->',
            '<p id="first">First block</p>',
            '<!-- event name: "event", block name: "context", template: "blocks/html/context.html.twig", priority: -5 -->',
            '<p class="context">The king is dead, long live the king!</p>',
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
}
