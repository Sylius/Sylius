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
use Sylius\Bundle\ThemeBundle\Model\ThemeAuthor;
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

    function it_has_id()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn(null);

        $this->setName('foo/bar');
        $this->getName()->shouldReturn('foo/bar');
    }

    function it_has_title()
    {
        $this->getTitle()->shouldReturn(null);

        $this->setTitle('Foo Bar');
        $this->getTitle()->shouldReturn('Foo Bar');
    }

    function it_has_path()
    {
        $this->getPath()->shouldReturn(null);

        $this->setPath('/foo/bar');
        $this->getPath()->shouldReturn('/foo/bar');
    }

    function it_has_code_based_on_md5ed_name()
    {
        $this->setName('name');

        $this->getCode()->shouldReturn(substr(md5('name'), 0, 8));
    }

    function it_has_description()
    {
        $this->getDescription()->shouldReturn(null);

        $this->setDescription('Lorem ipsum.');
        $this->getDescription()->shouldReturn('Lorem ipsum.');
    }

    function it_has_authors()
    {
        $themeAuthor = new ThemeAuthor();

        $this->getAuthors()->shouldHaveCount(0);

        $this->addAuthor($themeAuthor);
        $this->getAuthors()->shouldHaveCount(1);

        $this->removeAuthor($themeAuthor);
        $this->getAuthors()->shouldHaveCount(0);
    }

    function it_has_parents(ThemeInterface $theme)
    {
        $this->getParents()->shouldHaveCount(0);

        $this->addParent($theme);
        $this->getParents()->shouldHaveCount(1);

        $this->removeParent($theme);
        $this->getParents()->shouldHaveCount(0);
    }
}
