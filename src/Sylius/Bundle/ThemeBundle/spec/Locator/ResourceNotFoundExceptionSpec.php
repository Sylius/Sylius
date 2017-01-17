<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Locator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ResourceNotFoundExceptionSpec extends ObjectBehavior
{
    function let(ThemeInterface $theme)
    {
        $theme->getName()->willReturn('theme/name');

        $this->beConstructedWith('resource name', $theme);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ResourceNotFoundException::class);
    }

    function it_is_a_runtime_exception()
    {
        $this->shouldHaveType(\RuntimeException::class);
    }

    function it_has_custom_message()
    {
        $this->getMessage()->shouldReturn('Could not find resource "resource name" using theme "theme/name".');
    }
}
