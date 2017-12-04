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

namespace spec\Sylius\Bundle\ThemeBundle\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Model\Theme;
use Sylius\Bundle\ThemeBundle\Model\ThemeAuthor;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeScreenshot;

final class ThemeSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('theme/name', '/theme/path');
    }

    function it_implements_theme_interface(): void
    {
        $this->shouldImplement(ThemeInterface::class);
    }

    function its_name_cannot_have_underscores(): void
    {
        $this->beConstructedWith('first_theme/name', '/theme/path');

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_has_immutable_name(): void
    {
        $this->getName()->shouldReturn('theme/name');
    }

    function its_name_might_contain_numbers(): void
    {
        $this->beConstructedWith('1e/e7', '/theme/path');

        $this->getName()->shouldReturn('1e/e7');
    }

    function its_name_might_contain_uppercase_characters(): void
    {
        $this->beConstructedWith('AbC/DeF', '/theme/path');

        $this->getName()->shouldReturn('AbC/DeF');
    }

    function it_has_immutable_path(): void
    {
        $this->getPath()->shouldReturn('/theme/path');
    }

    function it_has_title(): void
    {
        $this->getTitle()->shouldReturn(null);

        $this->setTitle('Foo Bar');
        $this->getTitle()->shouldReturn('Foo Bar');
    }

    function it_has_description(): void
    {
        $this->getDescription()->shouldReturn(null);

        $this->setDescription('Lorem ipsum.');
        $this->getDescription()->shouldReturn('Lorem ipsum.');
    }

    function it_has_authors(): void
    {
        $themeAuthor = new ThemeAuthor();

        $this->getAuthors()->shouldHaveCount(0);

        $this->addAuthor($themeAuthor);
        $this->getAuthors()->shouldHaveCount(1);

        $this->removeAuthor($themeAuthor);
        $this->getAuthors()->shouldHaveCount(0);
    }

    function it_has_parents(ThemeInterface $theme): void
    {
        $this->getParents()->shouldHaveCount(0);

        $this->addParent($theme);
        $this->getParents()->shouldHaveCount(1);

        $this->removeParent($theme);
        $this->getParents()->shouldHaveCount(0);
    }

    function it_has_screenshots(): void
    {
        $themeScreenshot = new ThemeScreenshot('some path');

        $this->getScreenshots()->shouldHaveCount(0);

        $this->addScreenshot($themeScreenshot);
        $this->getScreenshots()->shouldHaveCount(1);

        $this->removeScreenshot($themeScreenshot);
        $this->getScreenshots()->shouldHaveCount(0);
    }
}
