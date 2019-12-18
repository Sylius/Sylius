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
    /** @test */
    public function it_renders_template_events_blocks(): void
    {
        self::bootKernel();

        /** @var Environment $twig */
        $twig = self::$container->get('twig');

        // See Kernel.php for the configuration resulting in those lines
        $expectedLines = [
            'First block',
            'Second block',
            'Third block',
            'The king is dead, long live the king!',
        ];
        $renderedLines = array_values(array_filter(explode("\n", $twig->render('templateEvents.txt.twig'))));

        Assert::assertSame($expectedLines, $renderedLines);
    }
}
