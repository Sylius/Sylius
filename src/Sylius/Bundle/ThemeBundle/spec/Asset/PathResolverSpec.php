<?php

namespace spec\Sylius\Bundle\ThemeBundle\Asset;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Asset\PathResolver;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @mixin PathResolver
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class PathResolverSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Asset\PathResolver');
    }

    function it_implements_path_resolver_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ThemeBundle\Asset\PathResolverInterface');
    }

    function it_returns_modified_path(ThemeInterface $theme)
    {
        $theme->getHashCode()->shouldBeCalled()->willReturn("HASHCODE");

        $this->resolve('/long/path/asset.min.js', $theme)->shouldReturn('/long/path-HASHCODE/asset.min.js');
    }

    function it_changes_only_last_dirname(ThemeInterface $theme)
    {
        $theme->getHashCode()->shouldBeCalled()->willReturn("HASHCODE");

        $this->resolve('/long.path/asset.min.js', $theme)->shouldReturn('/long.path-HASHCODE/asset.min.js');
    }
}