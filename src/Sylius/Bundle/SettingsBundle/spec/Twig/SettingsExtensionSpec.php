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
use Sylius\Bundle\SettingsBundle\Templating\Helper\SettingsHelperInterface;
use Sylius\Bundle\SettingsBundle\Twig\SettingsExtension;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class SettingsExtensionSpec extends ObjectBehavior
{
    function let(SettingsHelperInterface $helper)
    {
        $this->beConstructedWith($helper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SettingsExtension::class);
    }

    function it_should_be_a_twig_extension()
    {
        $this->shouldHaveType(\Twig_Extension::class);
    }
}
