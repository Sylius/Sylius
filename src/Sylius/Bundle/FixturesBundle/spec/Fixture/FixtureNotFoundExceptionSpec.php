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

namespace spec\Sylius\Bundle\FixturesBundle\Fixture;

use PhpSpec\ObjectBehavior;

final class FixtureNotFoundExceptionSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith('fixture_name');
    }

    public function it_is_an_invalid_argument_exception(): void
    {
        $this->shouldHaveType(\InvalidArgumentException::class);
    }

    public function it_has_preformatted_message(): void
    {
        $this->getMessage()->shouldReturn('Fixture with name "fixture_name" could not be found!');
    }
}
