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

namespace spec\Sylius\Bundle\InventoryBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Symfony\Component\Templating\Helper\Helper;

final class InventoryHelperSpec extends ObjectBehavior
{
    function let(AvailabilityCheckerInterface $checker): void
    {
        $this->beConstructedWith($checker);
    }

    function it_is_a_twig_extension(): void
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_delegates_the_stock_availability_checking_to_the_checker(
        AvailabilityCheckerInterface $checker,
        StockableInterface $stockable
    ): void {
        $checker->isStockAvailable($stockable)->shouldBeCalled()->willReturn(true);

        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    function it_delegates_the_stock_sufficiency_checking_to_the_checker(
        AvailabilityCheckerInterface $checker,
        StockableInterface $stockable
    ): void {
        $checker->isStockSufficient($stockable, 3)->shouldBeCalled()->willReturn(false);

        $this->isStockSufficient($stockable, 3)->shouldReturn(false);
    }
}
