<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UiBundle\Twig;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UiBundle\Twig\PercentageExtension;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class PercentageExtensionSpec extends ObjectBehavior
{
    function it_is_twig_extension()
    {
        $this->shouldHaveType(\Twig_Extension::class);
    }

    function it_returns_float_number_as_percentage()
    {
        $this->getPercentage(0.112)->shouldReturn('11.2 %');
    }
}
