<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\SettingsBundle\Twig;

use PhpSpec\ObjectBehavior;
use Sylius\SettingsBundle\Templating\Helper\SettingsHelperInterface;
use Sylius\SettingsBundle\Twig\SettingsExtension;

/**
 * @mixin SettingsExtension
 * 
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SettingsExtensionSpec extends ObjectBehavior
{
    function let(SettingsHelperInterface $helper)
    {
        $this->beConstructedWith($helper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\SettingsBundle\Twig\SettingsExtension');
    }

    function it_should_be_a_Twig_extension()
    {
        $this->shouldHaveType(\Twig_Extension::class);
    }
}
