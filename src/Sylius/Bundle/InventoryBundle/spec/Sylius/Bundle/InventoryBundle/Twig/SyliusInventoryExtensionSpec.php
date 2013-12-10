<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\InventoryBundle\Twig;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\InventoryBundle\Checker\AvailabilityCheckerInterface;
use Sylius\Bundle\InventoryBundle\Model\StockableInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusInventoryExtensionSpec extends ObjectBehavior
{
    function let(AvailabilityCheckerInterface $checker)
    {
        $this->beConstructedWith($checker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Twig\SyliusInventoryExtension');
    }

    function it_is_a_twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }

    function it_delegates_the_stock_availability_checking_to_the_checker($checker, StockableInterface $stockable)
    {
        $checker->isStockAvailable($stockable)->shouldBeCalled()->willReturn(true);

        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    function it_delegates_the_stock_sufficiency_checking_to_the_checker($checker, StockableInterface $stockable)
    {
        $checker->isStockSufficient($stockable, 3)->shouldBeCalled()->willReturn(false);

        $this->isStockSufficient($stockable, 3)->shouldReturn(false);
    }
}
