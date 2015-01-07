<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CurrencyBundle\Twig;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CurrencyBundle\Templating\Helper\CurrencyHelper;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CurrencyExtensionSpec extends ObjectBehavior
{
    function let(CurrencyHelper $helper)
    {
        $this->beConstructedWith($helper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CurrencyBundle\Twig\CurrencyExtension');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }
}
