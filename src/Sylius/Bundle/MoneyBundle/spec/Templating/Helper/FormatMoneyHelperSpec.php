<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\MoneyBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Bundle\MoneyBundle\Templating\Helper\FormatMoneyHelper;
use Sylius\Bundle\MoneyBundle\Templating\Helper\FormatMoneyHelperInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class FormatMoneyHelperSpec extends ObjectBehavior
{
    function let(MoneyFormatterInterface $moneyFormatter)
    {
        $this->beConstructedWith($moneyFormatter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FormatMoneyHelper::class);
    }

    function it_is_a_templating_helper()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_implements_format_money_helper_interface()
    {
        $this->shouldImplement(FormatMoneyHelperInterface::class);
    }

    function it_formats_money_using_given_currency_and_locale(MoneyFormatterInterface $moneyFormatter)
    {
        $moneyFormatter->format(2500, 'EUR', 'fr_FR')->willReturn('€25.00');

        $this->formatAmount(2500, 'EUR', 'fr_FR')->shouldReturn('€25.00');
    }
}
