<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Tests\Operator;

/**
 * Simple default operator test.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartOperatorTest extends \PHPUnit_Framework_TestCase
{
    public function testAddItemAddsItemToCart()
    {
        $item = $this->getMockItem();

        $cart = $this->getMockCart();
        $cart->expects($this->once())
            ->method('addItem')
            ->with($item)
        ;

        $cartOperator = $this->getCartOperator();
        $cartOperator->addItem($cart, $item);
    }

    public function testRefreshSetsTotalItems()
    {
        $cart = $this->getMockCart();
        $cart->expects($this->once())
            ->method('countItems')
            ->will($this->returnValue(6))
        ;
        $cart->expects($this->once())
            ->method('setTotalItems')
            ->with($this->equalTo(6))
        ;

        $cartOperator = $this->getCartOperator();
        $cartOperator->refresh($cart);
    }

    public function testClearRemovesCart()
    {
        $cart = $this->getMockCart();

        $cartManager = $this->getMockCartManager();
        $cartManager->expects($this->once())
            ->method('removeCart')
            ->with($this->equalTo($cart))
        ;

        $cartOperator = $this->getCartOperator($cartManager);
        $cartOperator->clear($cart);
    }

    public function testSavePersistsCart()
    {
        $cart = $this->getMockCart();

        $cartManager = $this->getMockCartManager();
        $cartManager->expects($this->once())
            ->method('persistCart')
            ->with($this->equalTo($cart))
        ;

        $cartOperator = $this->getCartOperator($cartManager);
        $cartOperator->save($cart);
    }

    public function testRemoveItemRemovesItemFromCart()
    {
        $item = $this->getMockItem();

        $cart = $this->getMockCart();
        $cart->expects($this->once())
            ->method('removeItem')
            ->with($item)
        ;

        $cartOperator = $this->getCartOperator();
        $cartOperator->removeItem($cart, $item);
    }

    private function getCartOperator($cartManager = null)
    {
        if (null === $cartManager) {
            $cartManager = $this->getMockCartManager();
        }

        return $this->getMockBuilder('Sylius\Bundle\CartBundle\Operator\CartOperator')
            ->setConstructorArgs(array($cartManager))
            ->getMockForAbstractClass()
        ;
    }

    private function getCart()
    {
        return $this->getMockForAbstractClass('Sylius\Bundle\CartBundle\Model\Cart');
    }

    private function getItem()
    {
        return $this->getMockForAbstractClass('Sylius\Bundle\CartBundle\Model\Item');
    }

    private function getMockCartManager()
    {
        return $this->getMock('Sylius\Bundle\CartBundle\Model\CartManagerInterface');
    }

    private function getMockCart()
    {
        return $this->getMock('Sylius\Bundle\CartBundle\Model\CartInterface');
    }

    private function getMockItem()
    {
        return $this->getMock('Sylius\Bundle\CartBundle\Model\ItemInterface');
    }
}
