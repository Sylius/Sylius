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
use Sylius\Bundle\LocaleBundle\Twig\LocaleExtension;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class LocaleExtensionSpec extends ObjectBehavior
{
    function let(LocaleHelperInterface $localeHelper)
    {
        $this->beConstructedWith($localeHelper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(LocaleExtension::class);
    }

    function it_is_a_twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }
}
