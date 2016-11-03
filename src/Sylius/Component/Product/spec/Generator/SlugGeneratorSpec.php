<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Product\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Generator\SlugGenerator;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class SlugGeneratorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SlugGenerator::class);
    }

    function it_implements_slug_generator_interface()
    {
        $this->shouldImplement(SlugGeneratorInterface::class);
    }

    function it_generates_slug_based_on_given_name()
    {
        $this->generate('Cyclades')->shouldReturn('cyclades');
        $this->generate('Small World')->shouldReturn('small-world');
    }

    function it_generates_slug_without_punctuation_marks()
    {
        $this->generate('"Ticket to Ride: Europe"')->shouldReturn('ticket-to-ride-europe');
        $this->generate('Tzolk\'in: The Mayan Calendar')->shouldReturn('tzolk-in-the-mayan-calendar');
        $this->generate('Game of Thrones: The Board Game')->shouldReturn('game-of-thrones-the-board-game');
    }

    function it_generates_slug_without_special_signs()
    {
        $this->generate('Wsiąść do Pociągu: Europa')->shouldReturn('wsiasc-do-pociagu-europa');
    }
}
