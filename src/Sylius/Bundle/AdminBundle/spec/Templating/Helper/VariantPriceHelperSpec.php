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

namespace spec\Sylius\Bundle\AdminBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Symfony\Component\Templating\Helper\Helper;

final class VariantPriceHelperSpec extends ObjectBehavior
{
    function let(
        ChannelRepositoryInterface $channelRepository,
        MoneyFormatterInterface $moneyFormatter
    ): void {
        $this->beConstructedWith($channelRepository, $moneyFormatter);
    }

    function it_is_templating_helper(): void
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_provides_product_price_with_base_currency_for_chosen_channel(
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
        CurrencyInterface $currency,
        MoneyFormatterInterface $moneyFormatter
    ): void {
        $channelRepository->findOneByCode('US_WEB')->willReturn($channel);
        $channel->getBaseCurrency()->willReturn($currency);
        $currency->getCode()->willReturn('USD');
        $moneyFormatter->format(10000, 'USD')->willReturn('$100.00');

        $this->getPriceWithCurrency(10000, 'US_WEB')->shouldReturn('$100.00');
    }
}
