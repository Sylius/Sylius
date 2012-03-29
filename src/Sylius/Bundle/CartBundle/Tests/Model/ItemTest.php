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

    private function getItem()
    {
        return $this->getMockForAbstractClass('Sylius\Bundle\CartBundle\Model\Item');
    }

    private function getMockCart()
    {
        return $this->getMock('Sylius\Bundle\CartBundle\Model\CartInterface');
    }
}

