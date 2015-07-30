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
        $this->shouldImplement('Sylius\Bundle\ThemeBundle\Model\ThemeInterface');
    }

    function it_has_name()
    {
        $this->setName("Foo Bar");
        $this->getName()->shouldReturn("Foo Bar");
    }

    function it_has_logical_name()
    {
        $this->setLogicalName("foo/bar");
        $this->getLogicalName()->shouldReturn("foo/bar");
    }

    function it_has_description()
    {
        $this->setDescription("Lorem ipsum.");
        $this->getDescription()->shouldReturn("Lorem ipsum.");
    }

    function it_has_path()
    {
        $this->setPath("/foo/bar");
        $this->getPath()->shouldReturn("/foo/bar");
    }

    function it_has_hash_code()
    {
        $logicalName = "hash/this";
        $this->setLogicalName($logicalName);

        $this->getHashCode()->shouldReturn(md5($logicalName));
    }
}
