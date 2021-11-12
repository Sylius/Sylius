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

namespace spec\Sylius\Component\Core\Promotion\Calculator;

use PhpSpec\ObjectBehavior;

final class MinimumPriceBasedPromotionAmountCalculatorSpec extends ObjectBehavior
{
    function it_calculates_promotion_based_on_product_minimum_price(): void
    {
        $this->calculate(2000, 1500, -1000)->shouldReturn(-500);
    }

    function it_returns_0_if_item_is_already_on_minimum_price(): void
    {
        $this->calculate(1500, 1500, -1000)->shouldReturn(0);
    }
}
