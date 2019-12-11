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

namespace spec\Sylius\Bundle\UiBundle\Twig;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Twig\Environment;

final class TemplateEventExtensionSpec extends ObjectBehavior
{
    function it_renders_blocks_for_given_event(Environment $twig): void
    {
        $this->beConstructedWith($twig, [
            'event_name' => [
                'first.html.twig',
                'second.html.twig',
            ],
        ]);

        $twig->render('first.html.twig', ['option' => 'value'])->willReturn('First template');
        $twig->render('second.html.twig', ['option' => 'value'])->willReturn('Second template');

        $this->renderBlocksForEvent('event_name', ['option' => 'value'])->shouldReturn(
            "First template\nSecond template"
        );
    }

    function it_returns_empty_string_if_there_are_no_blocks_registered_for_given_event(Environment $twig): void
    {
        $this->beConstructedWith($twig, ['event_name' => ['first.html.twig']]);

        $twig->render(Argument::cetera())->shouldNotBeCalled();

        $this->renderBlocksForEvent('another_event')->shouldReturn('');
    }
}
