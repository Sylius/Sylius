<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CurrencyBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Currency\Context\CurrencyContextInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class MoneyHelperSpec extends ObjectBehavior
{
    function let(CurrencyContextInterface $currencyContext)
    {
        $this->beConstructedWith('fr_FR', $currencyContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CurrencyBundle\Templating\Helper\MoneyHelper');
    }
}
