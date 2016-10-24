<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Templating\Helper\PriceHelper;
use Sylius\Bundle\MoneyBundle\Templating\Helper\PriceHelperInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Symfony\Component\Templating\Helper\HelperInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PriceHelperSpec extends ObjectBehavior
{
    function let(PriceHelperInterface $decoratedHelper, CurrencyContextInterface $currencyContext)
    {
        $this->beConstructedWith($decoratedHelper, $currencyContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PriceHelper::class);
    }

    function it_is_a_templating_helper()
    {
        $this->shouldImplement(HelperInterface::class);
    }

    function it_is_a_money_helper()
    {
        $this->shouldImplement(PriceHelperInterface::class);
    }

    function it_does_nothing_if_currency_is_passed(
        PriceHelperInterface $decoratedHelper,
        CurrencyContextInterface $currencyContext
    ) {
        $currencyContext->getCurrencyCode()->shouldNotBeCalled();

        $decoratedHelper->convertAndFormatAmount(42, 'USD', 1.0, null)->willReturn('$0.42');

        $this->convertAndFormatAmount(42, 'USD', 1.0)->shouldReturn('$0.42');
    }

    function it_does_nothing_if_currency_is_not_passed_and_there_is_no_current_one(
        PriceHelperInterface $decoratedHelper,
        CurrencyContextInterface $currencyContext
    ) {
        $currencyContext->getCurrencyCode()->willReturn(null);

        $decoratedHelper->convertAndFormatAmount(42, 'USD', 1.0, null)->willReturn('$0.42');

        $this->convertAndFormatAmount(42, 'USD', 1.0)->shouldReturn('$0.42');
    }

    function it_decorates_the_helper_with_current_currency_if_it_is_not_passed(
        PriceHelperInterface $decoratedHelper,
        CurrencyContextInterface $currencyContext
    ) {
        $currencyContext->getCurrencyCode()->willReturn('EUR');

        $decoratedHelper->convertAndFormatAmount(42, 'EUR', null, null)->willReturn('€0.42');

        $this->convertAndFormatAmount(42)->shouldReturn('€0.42');
    }
}
