<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\InventoryBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Inventory\Model\StockableInterface;

class InventoryHelperSpec extends ObjectBehavior
{
    public function let(AvailabilityCheckerInterface $checker)
    {
        $this->beConstructedWith($checker);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Templating\Helper\InventoryHelper');
    }

    public function it_is_a_twig_extension()
    {
        $this->shouldHaveType('Symfony\Component\Templating\Helper\Helper');
    }

    public function it_delegates_the_stock_availability_checking_to_the_checker(
        $checker,
        StockableInterface $stockable
    ) {
        $checker->isStockAvailable($stockable)->shouldBeCalled()->willReturn(true);

        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    public function it_delegates_the_stock_sufficiency_checking_to_the_checker(
        $checker,
        StockableInterface $stockable
    ) {
        $checker->isStockSufficient($stockable, 3)->shouldBeCalled()->willReturn(false);

        $this->isStockSufficient($stockable, 3)->shouldReturn(false);
    }
}
