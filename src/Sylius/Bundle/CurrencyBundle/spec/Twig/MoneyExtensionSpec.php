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
use Sylius\Component\Currency\Context\CurrencyContextInterface;

class MoneyExtensionSpec extends ObjectBehavior
{
    function let(CurrencyContextInterface $currencyContext)
    {
        $this->beConstructedWith('fr_FR', $currencyContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CurrencyBundle\Twig\MoneyExtension');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }
}
