<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\LocaleBundle\Twig;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelperInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LocaleExtensionSpec extends ObjectBehavior
{
    function let(LocaleHelperInterface $localeHelper)
    {
        $this->beConstructedWith($localeHelper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\LocaleBundle\Twig\LocaleExtension');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }
}
