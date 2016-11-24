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
use Sylius\Bundle\CoreBundle\Templating\Helper\ChannelBasedPriceHelper;
use Sylius\Bundle\CoreBundle\Templating\Helper\ChannelBasedPriceHelperInterface;
use Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ChannelBasedPriceHelperSpec extends ObjectBehavior
{
    function let(
        CartContextInterface $cartContext,
        ProductVariantPriceCalculatorInterface $productVariantPriceCalculator
    ) {
        $this->beConstructedWith($cartContext, $productVariantPriceCalculator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ChannelBasedPriceHelper::class);
    }

    function it_is_helper()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_implements_channel_based_price_helper_interface()
    {
        $this->shouldImplement(ChannelBasedPriceHelperInterface::class);
    }

    function it_returns_variant_price_for_current_channel(
        CartContextInterface $cartContext,
        ChannelInterface $currentChannel,
        OrderInterface $currentCart,
        ProductVariantInterface $productVariant,
        ProductVariantPriceCalculatorInterface $productVariantPriceCalculator
    ) {
        $cartContext->getCart()->willReturn($currentCart);
        $currentCart->getChannel()->willReturn($currentChannel);

        $productVariantPriceCalculator->calculate($productVariant, ['channel' => $currentChannel])->willReturn(1000);

        $this->getPriceForCurrentChannel($productVariant)->shouldReturn(1000);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_channel_variant_price');
    }
}
