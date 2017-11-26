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

namespace spec\Sylius\Bundle\ThemeBundle\Loader;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

final class CircularDependencyFoundExceptionSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith([]);
    }

    public function it_is_a_domain_exception(): void
    {
        $this->shouldHaveType(\DomainException::class);
    }

    public function it_is_a_logic_exception(): void
    {
        $this->shouldHaveType(\LogicException::class);
    }

    public function it_transforms_a_cycle_to_user_friendly_message(
        ThemeInterface $firstTheme,
        ThemeInterface $secondTheme,
        ThemeInterface $thirdTheme,
        ThemeInterface $fourthTheme
    ): void {
        $this->beConstructedWith([$firstTheme, $secondTheme, $thirdTheme, $fourthTheme, $thirdTheme]);

        $firstTheme->getName()->willReturn('first/theme');
        $secondTheme->getName()->willReturn('second/theme');
        $thirdTheme->getName()->willReturn('third/theme');
        $fourthTheme->getName()->willReturn('fourth/theme');

        $this->getMessage()->shouldReturn('Circular dependency was found while resolving theme "first/theme", caused by cycle "third/theme -> fourth/theme -> third/theme".');
    }

    public function it_throws_another_exception_if_there_is_no_cycle_in_given_elements(
        ThemeInterface $firstTheme,
        ThemeInterface $secondTheme,
        ThemeInterface $thirdTheme,
        ThemeInterface $fourthTheme
    ): void {
        $this->beConstructedWith([$firstTheme, $secondTheme, $thirdTheme, $fourthTheme]);

        $this->shouldThrow(new \InvalidArgumentException('There is no cycle within given themes.'))->duringInstantiation();
    }
}
