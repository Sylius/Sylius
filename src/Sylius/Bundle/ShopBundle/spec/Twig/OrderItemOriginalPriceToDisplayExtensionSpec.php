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

namespace spec\Sylius\Bundle\ShopBundle\Twig;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItem;
use Twig\Extension\AbstractExtension;

final class OrderItemOriginalPriceToDisplayExtensionSpec extends ObjectBehavior
{
    function it_implements_a_twig_abstract_extension(): void
    {
        $this->shouldImplement(AbstractExtension::class);
    }

    function it_returns_an_original_unit_price_if_it_is_greater_than_other_prices(OrderItem $item): void
    {
        $item->getUnitPrice()->willReturn(1000);
        $item->getDiscountedUnitPrice()->willReturn(800);
        $item->getOriginalUnitPrice()->willReturn(5000);

        $this->getOriginalPriceToDisplay($item)->shouldReturn(5000);
    }

    function it_returns_an_unit_price_if_it_is_greater_than_original_unit_price_and_discounted_unit_price(OrderItem $item): void
    {
        $item->getUnitPrice()->willReturn(1000);
        $item->getDiscountedUnitPrice()->willReturn(800);
        $item->getOriginalUnitPrice()->willReturn(null);

        $this->getOriginalPriceToDisplay($item)->shouldReturn(1000);
    }
}
