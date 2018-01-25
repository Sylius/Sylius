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

namespace spec\Sylius\Bundle\ThemeBundle\Locator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

final class ResourceNotFoundExceptionSpec extends ObjectBehavior
{
    function let(ThemeInterface $theme): void
    {
        $theme->getName()->willReturn('theme/name');

        $this->beConstructedWith('resource name', $theme);
    }

    function it_is_a_runtime_exception(): void
    {
        $this->shouldHaveType(\RuntimeException::class);
    }

    function it_has_custom_message(): void
    {
        $this->getMessage()->shouldReturn('Could not find resource "resource name" using theme "theme/name".');
    }
}
