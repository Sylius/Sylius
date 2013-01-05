<?php

namespace spec\Sylius\Bundle\InventoryBundle\Twig;

use PHPSpec2\ObjectBehavior;

/**
 * Sylius inventory extensions for Twig spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusInventoryExtension extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\InventoryBundle\Checker\AvailabilityCheckerInterface $checker
     */
    function let($checker)
    {
        $this->beConstructedWith($checker);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Twig\SyliusInventoryExtension');
    }

    function it_should_be_a_twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_should_delegate_the_stock_availability_checking_to_the_checker($checker, $stockable)
    {
        $checker->isStockAvailable($stockable)->shouldBeCalled()->willReturn(true);

        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_should_delegate_the_stock_sufficiency_checking_to_the_checker($checker, $stockable)
    {
        $checker->isStockSufficient($stockable, 3)->shouldBeCalled()->willReturn(false);

        $this->isStockSufficient($stockable, 3)->shouldReturn(false);
    }
}
