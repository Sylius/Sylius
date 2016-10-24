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
use Sylius\Bundle\ThemeBundle\Model\ThemeScreenshot;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeScreenshotSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('/screenshot/path.jpg');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ThemeScreenshot::class);
    }

    function it_has_path()
    {
        $this->beConstructedWith('/my/screenshot.jpg');

        $this->getPath()->shouldReturn('/my/screenshot.jpg');
    }

    function it_has_title()
    {
        $this->getTitle()->shouldReturn(null);

        $this->setTitle('Candy shop');
        $this->getTitle()->shouldReturn('Candy shop');
    }

    function it_has_description()
    {
        $this->getDescription()->shouldReturn(null);

        $this->setDescription('I\'ll take you to the candy shop');
        $this->getDescription()->shouldReturn('I\'ll take you to the candy shop');
    }
}
