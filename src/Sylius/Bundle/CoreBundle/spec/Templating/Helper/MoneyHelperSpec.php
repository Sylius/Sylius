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
use Sylius\Bundle\CoreBundle\Templating\Helper\MoneyHelper;
use Sylius\Bundle\MoneyBundle\Templating\Helper\MoneyHelperInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\Templating\Helper\HelperInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class MoneyHelperSpec extends ObjectBehavior
{
    function let(MoneyHelperInterface $decoratedHelper, LocaleContextInterface $localeContext)
    {
        $this->beConstructedWith($decoratedHelper, $localeContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MoneyHelper::class);
    }

    function it_is_a_templating_helper()
    {
        $this->shouldImplement(HelperInterface::class);
    }

    function it_is_a_money_helper()
    {
        $this->shouldImplement(MoneyHelperInterface::class);
    }

    function it_does_nothing_if_locale_is_passed(
        MoneyHelperInterface $decoratedHelper,
        LocaleContextInterface $localeContext
    ) {
        $localeContext->getLocaleCode()->shouldNotBeCalled();

        $decoratedHelper->formatAmount(42, null, 'en_US')->willReturn('Formatted 42 in en_US');

        $this->formatAmount(42, null, 'en_US')->shouldReturn('Formatted 42 in en_US');
    }

    function it_decorates_the_helper_with_current_locale_if_it_is_not_passed(
        MoneyHelperInterface $decoratedHelper,
        LocaleContextInterface $localeContext
    ) {
        $localeContext->getLocaleCode()->willReturn('fr_FR');

        $decoratedHelper->formatAmount(42, null, 'fr_FR')->willReturn('Formatted 42 in fr_FR');

        $this->formatAmount(42)->shouldReturn('Formatted 42 in fr_FR');
    }
}
