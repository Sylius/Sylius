<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Loader;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Loader\CircularDependencyFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CircularDependencyFoundExceptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CircularDependencyFoundException::class);
    }

    function it_is_a_domain_exception()
    {
        $this->shouldHaveType(\DomainException::class);
    }

    function it_is_a_logic_exception()
    {
        $this->shouldHaveType(\LogicException::class);
    }

    function it_transforms_a_cycle_to_user_friendly_message(
        ThemeInterface $firstTheme,
        ThemeInterface $secondTheme,
        ThemeInterface $thirdTheme,
        ThemeInterface $fourthTheme
    ) {
        $this->beConstructedWith([$firstTheme, $secondTheme, $thirdTheme, $fourthTheme, $thirdTheme]);

        $firstTheme->getName()->willReturn('first/theme');
        $secondTheme->getName()->willReturn('second/theme');
        $thirdTheme->getName()->willReturn('third/theme');
        $fourthTheme->getName()->willReturn('fourth/theme');

        $this->getMessage()->shouldReturn('Circular dependency was found while resolving theme "first/theme", caused by cycle "third/theme -> fourth/theme -> third/theme".');
    }

    function it_throws_another_exception_if_there_is_no_cycle_in_given_elements(
        ThemeInterface $firstTheme,
        ThemeInterface $secondTheme,
        ThemeInterface $thirdTheme,
        ThemeInterface $fourthTheme
    ) {
        $this->beConstructedWith([$firstTheme, $secondTheme, $thirdTheme, $fourthTheme]);

        $this->shouldThrow(new \InvalidArgumentException('There is no cycle within given themes.'))->duringInstantiation();
    }
}
