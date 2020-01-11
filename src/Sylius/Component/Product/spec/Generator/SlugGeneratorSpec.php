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

namespace spec\Sylius\Component\Product\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;

final class SlugGeneratorSpec extends ObjectBehavior
{
    function it_implements_slug_generator_interface(): void
    {
        $this->shouldImplement(SlugGeneratorInterface::class);
    }

    function it_generates_slug_based_on_given_name(): void
    {
        $this->generate('Cyclades')->shouldReturn('cyclades');
        $this->generate('Small World')->shouldReturn('small-world');
    }

    function it_generates_slug_without_punctuation_marks(): void
    {
        $this->generate('"Ticket to Ride: Europe"')->shouldReturn('ticket-to-ride-europe');
        $this->generate('Tzolk\'in: The Mayan Calendar')->shouldReturn('tzolk-in-the-mayan-calendar');
        $this->generate('Game of Thrones: The Board Game')->shouldReturn('game-of-thrones-the-board-game');
    }

    function it_generates_slug_without_special_signs(): void
    {
        $this->generate('Wsiąść do Pociągu: Europa')->shouldReturn('wsiasc-do-pociagu-europa');
    }
}
