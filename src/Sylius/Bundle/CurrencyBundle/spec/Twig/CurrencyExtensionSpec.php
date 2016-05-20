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
use Sylius\Bundle\CurrencyBundle\Templating\Helper\CurrencyHelperInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CurrencyExtensionSpec extends ObjectBehavior
{
    function let(CurrencyHelperInterface $helper)
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
