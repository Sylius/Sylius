<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Tests\Model;

/**
 * Item model test.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $item = $this->getItem();

        $this->assertEquals(1, $item->getQuantity());
    }

    public function testGetSetId()
    {
        $item = $this->getItem();
        $this->assertNull($item->getId());

        $item->setId(5);
        $this->assertEquals(5, $item->getId());
    }

    public function testGetSetCart()
    {
        $item = $this->getItem();
        $this->assertNull($item->getCart());

        $cart = $this->getMockCart();

        $item->setCart($cart);
        $this->assertEquals($cart, $item->getCart());
    }

    public function testEquals()
    {
        $item = $this->getItem();
        $item->setId(1);

        $duplicatedItem = clone $item;
        $this->assertTrue($item->equals($duplicatedItem));

        $duplicatedItem->setQuantity(2);
        $this->assertTrue($item->equals($duplicatedItem));
        $this->assertNotEquals($duplicatedItem->getQuantity(), $item->getQuantity());

        $duplicatedItem->setId(2);
        $this->assertFalse($item->equals($duplicatedItem));
    }

    public function testQuantityManagement()
    {
        $item = $this->getItem();
        $item->setQuantity(5);
        $this->assertEquals(5, $item->getQuantity());

        $item->incrementQuantity(5);
        $this->assertEquals(10, $item->getQuantity());

        $item->incrementQuantity(-10);
        $this->assertEquals(1, $item->getQuantity());
    }

    /**
     * @expectedException \OutOfRangeException
     */
    public function testQuantityMustBeBiggerThanZero()
    {
        $item = $this->getItem();
        $item->setQuantity(0);
    }

    /**
     * @return \Sylius\Bundle\CartBundle\Model\Item
     */
    private function getItem()
    {
        return $this->getMockForAbstractClass('Sylius\Bundle\CartBundle\Model\Item');
    }

    /**
     * @return \Sylius\Bundle\CartBundle\Model\CartInterface
     */
    private function getMockCart()
    {
        return $this->getMock('Sylius\Bundle\CartBundle\Model\CartInterface');
    }
}

