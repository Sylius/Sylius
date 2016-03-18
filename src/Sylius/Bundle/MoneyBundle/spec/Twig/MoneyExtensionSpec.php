<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\MoneyBundle\Twig;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\MoneyBundle\Templating\Helper\MoneyHelperInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class MoneyExtensionSpec extends ObjectBehavior
{
    function let(MoneyHelperInterface $helper)
    {
        $this->beConstructedWith($helper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\Twig\MoneyExtension');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }
}
