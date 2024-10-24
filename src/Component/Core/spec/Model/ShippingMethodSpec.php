<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShippingMethod;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingMethod as BaseShippingMethod;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

final class ShippingMethodSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(ShippingMethod::class);
    }

    function it_implements_a_shipping_method_interface(): void
    {
        $this->shouldImplement(ShippingMethodInterface::class);
    }

    function it_extends_a_shipping_method(): void
    {
        $this->shouldHaveType(BaseShippingMethod::class);
    }

    function it_does_not_have_any_zone_defined_by_default(): void
    {
        $this->getZone()->shouldReturn(null);
    }

    function it_allows_defining_zone(ZoneInterface $zone): void
    {
        $this->setZone($zone);
        $this->getZone()->shouldReturn($zone);
    }

    function its_tax_category_is_mutable(TaxCategoryInterface $category): void
    {
        $this->setTaxCategory($category);
        $this->getTaxCategory()->shouldReturn($category);
    }

    function it_has_channels_collection(ChannelInterface $firstChannel, ChannelInterface $secondChannel): void
    {
        $this->addChannel($firstChannel);
        $this->addChannel($secondChannel);

        $this->getChannels()->shouldIterateAs([$firstChannel, $secondChannel]);
    }

    function it_can_add_and_remove_channels(ChannelInterface $channel): void
    {
        $this->addChannel($channel);
        $this->hasChannel($channel)->shouldReturn(true);

        $this->removeChannel($channel);
        $this->hasChannel($channel)->shouldReturn(false);
    }
}
