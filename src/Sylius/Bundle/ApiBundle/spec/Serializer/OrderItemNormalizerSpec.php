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

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class OrderItemNormalizerSpec extends ObjectBehavior
{
    function let(
        ChannelContextInterface $channelContext,
        SectionProviderInterface $sectionProvider
    ): void {
        $this->beConstructedWith($channelContext, $sectionProvider);
    }

    function it_supports_only_order_item_interface(OrderItemInterface $orderItem, OrderInterface $order): void
    {
        $this->supportsNormalization($orderItem)->shouldReturn(true);
        $this->supportsNormalization($order)->shouldReturn(false);
    }

    function it_supports_normalization_if_section_is_not_admin_get(
        OrderItemInterface $orderItem,
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);
        $this->supportsNormalization($orderItem)->shouldReturn(true);
    }

    function it_does_not_support_if_section_is_admin_get(
        OrderItemInterface $orderItem,
        SectionProviderInterface $sectionProvider,
        AdminApiSection $adminApiSection
    ): void {
        $sectionProvider->getSection()->willReturn($adminApiSection);
        $this->supportsNormalization($orderItem)->shouldReturn(false);
    }

    function it_does_not_support_if_the_normalizer_has_been_already_called(OrderItemInterface $orderItem): void
    {
        $this
            ->supportsNormalization($orderItem, null, ['sylius_order_item_normalizer_already_called' => true])
            ->shouldReturn(false)
        ;
    }

    function it_serializes_order_item_if_item_operation_name_is_different_that_admin_get(
        ChannelContextInterface $channelContext,
        NormalizerInterface $normalizer,
        OrderItemInterface $orderItem,
        ChannelInterface $channel,
        ProductVariantInterface $variant,
        ChannelPricingInterface $channelPricing
    ): void {
        $this->setNormalizer($normalizer);

        $normalizer->normalize($orderItem, null, ['sylius_order_item_normalizer_already_called' => true])->willReturn([]);

        $channelContext->getChannel()->willReturn($channel);

        $orderItem->getVariant()->willReturn($variant);

        $variant->getAppliedPromotionsForChannel($channel)->willReturn(new ArrayCollection());
        $variant->getChannelPricingForChannel($channel)->willReturn($channelPricing);

        $channelPricing->getOriginalPrice()->willReturn(20);

        $this->normalize($orderItem, null, [])->shouldReturn(['originalPrice' => 20]);
    }

    function it_throws_an_exception_if_the_normalizer_has_been_already_called(
        NormalizerInterface $normalizer,
        OrderItemInterface $orderItem
    ): void {
        $this->setNormalizer($normalizer);

        $normalizer->normalize($orderItem, null, ['sylius_order_item_normalizer_already_called' => true])->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [$orderItem, null, ['sylius_order_item_normalizer_already_called' => true]])
        ;
    }
}
