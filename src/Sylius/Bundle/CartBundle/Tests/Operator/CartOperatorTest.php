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

    public function testAddItemIncreasesQuantityIfThereAreItemsThatAreEqual()
    {
        $item = $this->getItem();
        $item->setId(1);
        $item->setQuantity(3);

        $cart = $this->getCart();

        $cartOperator = $this->getCartOperator();
        $cartOperator->addItem($cart, $item);

        $duplicatedItem = clone $item;
        $duplicatedItem->setQuantity(2);

        $cartOperator->addItem($cart, $duplicatedItem);

        $this->assertEquals(1, $cart->countItems());
        $this->assertEquals(5, $item->getQuantity());
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

    /**
     * @param object|null $cartManager
     *
     * @return \Sylius\Bundle\CartBundle\Operator\CartOperator
     */
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

    /**
     * @return \Sylius\Bundle\CartBundle\Model\Cart
     */
    private function getCart()
    {
        return $this->getMockForAbstractClass('Sylius\Bundle\CartBundle\Model\Cart');
    }

    /**
     * @return \Sylius\Bundle\CartBundle\Model\Item
     */
    private function getItem()
    {
        return $this->getMockForAbstractClass('Sylius\Bundle\CartBundle\Model\Item');
    }

    /**
     * @return \Sylius\Bundle\CartBundle\Model\CartManagerInterface
     */
    private function getMockCartManager()
    {
        return $this->getMock('Sylius\Bundle\CartBundle\Model\CartManagerInterface');
    }

    /**
     * @return \Sylius\Bundle\CartBundle\Model\CartInterface
     */
    private function getMockCart()
    {
        return $this->getMock('Sylius\Bundle\CartBundle\Model\CartInterface');
    }

    /**
     * @return \Sylius\Bundle\CartBundle\Model\ItemInterface
     */
    private function getMockItem()
    {
        return $this->getMock('Sylius\Bundle\CartBundle\Model\ItemInterface');
    }
}
