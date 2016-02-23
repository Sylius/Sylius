<?php

namespace spec\Sylius\Bundle\ThemeBundle\Synchronizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Synchronizer\CircularDependencyChecker;
use Sylius\Bundle\ThemeBundle\Synchronizer\CircularDependencyCheckerInterface;
use Sylius\Bundle\ThemeBundle\Synchronizer\CircularDependencyFoundException;

/**
 * @mixin CircularDependencyChecker
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class CircularDependencyCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Synchronizer\CircularDependencyChecker');
    }

    function it_implements_circular_dependency_checker_interface()
    {
        $this->shouldImplement(CircularDependencyCheckerInterface::class);
    }

    function it_does_not_find_circular_dependency_if_checking_a_theme_without_any_parents(
        ThemeInterface $theme
    ) {
        $theme->getParents()->willReturn([]);

        $this->check($theme);
    }

    function it_does_not_find_circular_dependency_if_theme_parents_are_not_cycled(
        ThemeInterface $firstTheme,
        ThemeInterface $secondTheme,
        ThemeInterface $thirdTheme,
        ThemeInterface $fourthTheme
    ) {
        $firstTheme->getParents()->willReturn([$secondTheme, $thirdTheme]);
        $secondTheme->getParents()->willReturn([$thirdTheme, $fourthTheme]);
        $thirdTheme->getParents()->willReturn([$fourthTheme]);
        $fourthTheme->getParents()->willReturn([]);

        $this->check($firstTheme);
    }

    function it_finds_circular_dependency_if_theme_parents_are_cycled(
        ThemeInterface $firstTheme,
        ThemeInterface $secondTheme,
        ThemeInterface $thirdTheme,
        ThemeInterface $fourthTheme
    ) {
        $firstTheme->getParents()->willReturn([$secondTheme, $thirdTheme]);
        $secondTheme->getParents()->willReturn([$thirdTheme]);
        $thirdTheme->getParents()->willReturn([$fourthTheme]);
        $fourthTheme->getParents()->willReturn([$secondTheme]);

        $firstTheme->getName()->willReturn('first/theme');
        $secondTheme->getName()->willReturn('second/theme');
        $thirdTheme->getName()->willReturn('third/theme');
        $fourthTheme->getName()->willReturn('fourth/theme');

        $this
            ->shouldThrow(CircularDependencyFoundException::class)
            ->during('check', [$firstTheme])
        ;
    }
}
