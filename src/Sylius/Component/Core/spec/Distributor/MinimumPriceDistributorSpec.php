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

namespace spec\Sylius\Component\Core\Distributor;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class MinimumPriceDistributorSpec extends ObjectBehavior
{
    function let(ProportionalIntegerDistributorInterface $proportionalIntegerDistributor): void
    {
        $this->beConstructedWith($proportionalIntegerDistributor);
    }

    function it_distributes_promotion_taking_into_account_minimum_price(
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        OrderItemInterface $tshirt,
        OrderItemInterface $book,
        OrderItemInterface $shoes,
        OrderItemInterface $boardGame,
        ProductVariantInterface $tshirtVariant,
        ProductVariantInterface $bookVariant,
        ProductVariantInterface $shoesVariant,
        ProductVariantInterface $boardGameVariant,
        ChannelPricingInterface $tshirtVariantChannelPricing,
        ChannelPricingInterface $bookVariantChannelPricing,
        ChannelPricingInterface $shoesVariantChannelPricing,
        ChannelPricingInterface $boardGameVariantChannelPricing,
        ChannelInterface $channel
    ) {
        $tshirt->getTotal()->willReturn(1000);
        $tshirt->getQuantity()->willReturn(1);
        $tshirt->getVariant()->willReturn($tshirtVariant);
        $tshirtVariant->getChannelPricingForChannel($channel)->willReturn($tshirtVariantChannelPricing);
        $tshirtVariantChannelPricing->getMinimumPrice()->willReturn(0);

        $book->getTotal()->willReturn(2000);
        $book->getQuantity()->willReturn(1);
        $book->getVariant()->willReturn($bookVariant);
        $bookVariant->getChannelPricingForChannel($channel)->willReturn($bookVariantChannelPricing);
        $bookVariantChannelPricing->getMinimumPrice()->willReturn(1900);

        $shoes->getTotal()->willReturn(5000);
        $shoes->getQuantity()->willReturn(1);
        $shoes->getVariant()->willReturn($shoesVariant);
        $shoesVariant->getChannelPricingForChannel($channel)->willReturn($shoesVariantChannelPricing);
        $shoesVariantChannelPricing->getMinimumPrice()->willReturn(5000);

        $boardGame->getTotal()->willReturn(3000);
        $boardGame->getQuantity()->willReturn(1);
        $boardGame->getVariant()->willReturn($boardGameVariant);
        $boardGameVariant->getChannelPricingForChannel($channel)->willReturn($boardGameVariantChannelPricing);
        $boardGameVariantChannelPricing->getMinimumPrice()->willReturn(2600);

        $proportionalIntegerDistributor->distribute([1000, 2000, 5000, 3000], -1200)->willReturn([-110, -218, -545, -327]);
        $proportionalIntegerDistributor->distribute([1000, 3000], -663)->willReturn([-166, -497]);
        $proportionalIntegerDistributor->distribute([1000], -424)->willReturn([-424]);

        $this->distribute([$tshirt, $book, $shoes, $boardGame], -1200, $channel)->shouldReturn([-700, -100, 0, -400]);
    }

    function it_distributes_promotion_taking_into_account_minimum_price_with_quantity(
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        OrderItemInterface $tshirt,
        OrderItemInterface $mug,
        ProductVariantInterface $tshirtVariant,
        ProductVariantInterface $mugVariant,
        ChannelPricingInterface $tshirtVariantChannelPricing,
        ChannelPricingInterface $mugVariantChannelPricing,
        ChannelInterface $channel
    ) {
        $tshirt->getTotal()->willReturn(5000);
        $tshirt->getQuantity()->willReturn(1);
        $tshirt->getVariant()->willReturn($tshirtVariant);
        $tshirtVariant->getChannelPricingForChannel($channel)->willReturn($tshirtVariantChannelPricing);
        $tshirtVariantChannelPricing->getMinimumPrice()->willReturn(4500);

        $mug->getTotal()->willReturn(6000);
        $mug->getQuantity()->willReturn(3);
        $mug->getVariant()->willReturn($mugVariant);
        $mugVariant->getChannelPricingForChannel($channel)->willReturn($mugVariantChannelPricing);
        $mugVariantChannelPricing->getMinimumPrice()->willReturn(0);

        $proportionalIntegerDistributor->distribute([5000, 6000], -2500)->willReturn([-1136, -1364]);
        $proportionalIntegerDistributor->distribute([6000], -636)->willReturn([-636]);

        $this->distribute([$tshirt, $mug], -2500, $channel)->shouldReturn([-500, -2000]);
    }

    function it_distributes_promotion_that_exceeds_possible_distribution_taking_into_account_minimum_price(
        ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        OrderItemInterface $tshirt,
        OrderItemInterface $mug,
        ProductVariantInterface $tshirtVariant,
        ProductVariantInterface $mugVariant,
        ChannelPricingInterface $tshirtVariantChannelPricing,
        ChannelPricingInterface $mugVariantChannelPricing,
        ChannelInterface $channel
    ) {
        $tshirt->getTotal()->willReturn(5000);
        $tshirt->getQuantity()->willReturn(1);
        $tshirt->getVariant()->willReturn($tshirtVariant);
        $tshirtVariant->getChannelPricingForChannel($channel)->willReturn($tshirtVariantChannelPricing);
        $tshirtVariantChannelPricing->getMinimumPrice()->willReturn(4500);

        $mug->getTotal()->willReturn(6000);
        $mug->getQuantity()->willReturn(3);
        $mug->getVariant()->willReturn($mugVariant);
        $mugVariant->getChannelPricingForChannel($channel)->willReturn($mugVariantChannelPricing);
        $mugVariantChannelPricing->getMinimumPrice()->willReturn(1500);

        $proportionalIntegerDistributor->distribute([5000, 6000], -2500)->willReturn([-1136, -1364]);
        $proportionalIntegerDistributor->distribute([6000], -636)->willReturn([-636]);

        $this->distribute([$tshirt, $mug], -2500, $channel)->shouldReturn([-500, -1500]);
    }
}
