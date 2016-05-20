<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\RbacBundle\Twig;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\RbacBundle\Templating\Helper\RbacHelper;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RbacExtensionSpec extends ObjectBehavior
{
    function let(RbacHelper $helper)
    {
        $this->beConstructedWith($helper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\RbacBundle\Twig\RbacExtension');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }
}
