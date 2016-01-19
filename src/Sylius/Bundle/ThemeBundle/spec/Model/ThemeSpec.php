<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Model\Theme;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @mixin Theme
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Model\Theme');
    }

    function it_implements_theme_interface()
    {
        $this->shouldImplement(ThemeInterface::class);
    }

    function it_implements_resource_interface()
    {
        $this->shouldImplement(ResourceInterface::class);
    }

    function its_id_is_slug()
    {
        $this->getId()->shouldReturn(null);

        $this->setSlug('slug');
        $this->getId()->shouldReturn('slug');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn(null);

        $this->setName("Foo Bar");
        $this->getName()->shouldReturn("Foo Bar");
    }

    function it_has_slug()
    {
        $this->getSlug()->shouldReturn(null);

        $this->setSlug("foo/bar");
        $this->getSlug()->shouldReturn("foo/bar");
    }

    function it_has_path()
    {
        $this->getPath()->shouldReturn(null);

        $this->setPath("/foo/bar");
        $this->getPath()->shouldReturn("/foo/bar");
    }

    function it_has_description()
    {
        $this->getDescription()->shouldReturn(null);

        $this->setDescription("Lorem ipsum.");
        $this->getDescription()->shouldReturn("Lorem ipsum.");
    }

    function it_has_code_based_on_md5ed_slug()
    {
        $this->setSlug('slug');

        $this->getCode()->shouldReturn(substr(md5('slug'), 0, 8));
    }
}
