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

namespace spec\Sylius\Bundle\CoreBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Templating\Helper\Helper;

final class PriceHelperSpec extends ObjectBehavior
{
    function let(ProductVariantPriceCalculatorInterface $productVariantPriceCalculator): void
    {
        $this->beConstructedWith($productVariantPriceCalculator);
    }

    function it_is_helper(): void
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_returns_variant_price_for_channel_given_in_context(
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        ProductVariantPriceCalculatorInterface $productVariantPriceCalculator
    ): void {
        $context = ['channel' => $channel];

        $productVariantPriceCalculator->calculate($productVariant, $context)->willReturn(1000);

        $this->getPrice($productVariant, $context)->shouldReturn(1000);
    }

    function it_throws_invalid_argument_exception_when_channel_key_is_not_present_in_context(
        ProductVariantInterface $productVariant,
        ProductVariantPriceCalculatorInterface $productVariantPriceCalculator
    ): void {
        $context = ['lennahc' => ''];

        $this->shouldThrow(\InvalidArgumentException::class)->during('getPrice', [$productVariant, $context]);

        $productVariantPriceCalculator->calculate($productVariant, $context)->shouldNotBeCalled();
    }

    function it_has_name(): void
    {
        $this->getName()->shouldReturn('sylius_calculate_price');
    }
}
