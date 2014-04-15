<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SettingsBundle\Twig;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\SettingsBundle\Templating\Helper\SettingsHelper;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SettingsExtensionSpec extends ObjectBehavior
{
    function let(SettingsHelper $helper)
    {
        $this->beConstructedWith($helper);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Twig\SettingsExtension');
    }

    function it_should_be_a_Twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }
}
