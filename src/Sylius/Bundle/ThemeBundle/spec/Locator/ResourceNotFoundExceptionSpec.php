<?php

namespace spec\Sylius\Bundle\ThemeBundle\Locator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @mixin ResourceNotFoundException
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ResourceNotFoundExceptionSpec extends ObjectBehavior
{
    function let(ThemeInterface $theme)
    {
        $theme->getSlug()->willReturn('theme/slug');

        $this->beConstructedWith('resource name', $theme);
    }
    
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException');
    }

    function it_is_a_runtime_exception()
    {
        $this->shouldHaveType(\RuntimeException::class);
    }

    function it_has_custom_message()
    {
        $this->getMessage()->shouldReturn('Could not find resource "resource name" using theme "theme/slug".');
    }
}
