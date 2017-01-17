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
use Sylius\Bundle\ThemeBundle\Model\ThemeScreenshot;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('theme/name', '/theme/path');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Theme::class);
    }

    function it_implements_theme_interface()
    {
        $this->shouldImplement(ThemeInterface::class);
    }

    function its_name_cannot_have_underscores()
    {
        $this->beConstructedWith('first_theme/name', '/theme/path');

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_has_immutable_name()
    {
        $this->getName()->shouldReturn('theme/name');
    }

    function it_has_immutable_path()
    {
        $this->getPath()->shouldReturn('/theme/path');
    }

    function it_has_title()
    {
        $this->getTitle()->shouldReturn(null);

        $this->setTitle('Foo Bar');
        $this->getTitle()->shouldReturn('Foo Bar');
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

    function it_has_screenshots()
    {
        $themeScreenshot = new ThemeScreenshot('some path');

        $this->getScreenshots()->shouldHaveCount(0);

        $this->addScreenshot($themeScreenshot);
        $this->getScreenshots()->shouldHaveCount(1);

        $this->removeScreenshot($themeScreenshot);
        $this->getScreenshots()->shouldHaveCount(0);
    }
}
