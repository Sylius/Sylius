<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\InventoryBundle\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class InventoryUnitSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Model\InventoryUnit');
    }

    function it_implements_Sylius_inventory_unit_interface()
    {
        $this->shouldImplement('Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_defined_stockable_subject_by_default()
    {
        $this->getStockable()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_allows_defining_stockable_subject($stockable)
    {
        $this->setStockable($stockable);
        $this->getStockable()->shouldReturn($stockable);
    }

    function it_has_sold_state_by_default()
    {
        $this->getInventoryState()->shouldReturn(InventoryUnitInterface::STATE_SOLD);
    }

    function its_state_is_mutable()
    {
        $this->setInventoryState(InventoryUnitInterface::STATE_BACKORDERED);
        $this->getInventoryState()->shouldReturn(InventoryUnitInterface::STATE_BACKORDERED);
    }

    function it_is_sold_if_its_state_says_so()
    {
        $this->shouldBeSold();
    }

    function it_is_backordered_if_its_state_says_so()
    {
        $this->setInventoryState(InventoryUnitInterface::STATE_BACKORDERED);
        $this->shouldBeBackordered();
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_returns_its_stockable_name($stockable)
    {
        $stockable->getInventoryName()->willReturn('[IPHONE5] iPhone 5');
        $this->setStockable($stockable);

        $this->getInventoryName()->shouldReturn('[IPHONE5] iPhone 5');
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_returns_its_stockable_sku($stockable)
    {
        $stockable->getSku()->willReturn('IPHONE5');
        $this->setStockable($stockable);

        $this->getSku()->shouldReturn('IPHONE5');
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function its_creation_date_is_mutable()
    {
        $date = new \DateTime('last year');

        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable()
    {
        $date = new \DateTime('last year');

        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
