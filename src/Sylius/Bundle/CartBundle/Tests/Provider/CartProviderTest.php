<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Tests\Provider;

use Sylius\Bundle\CartBundle\Provider\CartProvider;

/**
 * Default cart provider test.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartOperatorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCartReturnsNewCartWhenCartStorageReturnsNullIdentifier()
    {
        $cart = $this->getMockCart();

        $cartStorage = $this->getMockCartStorage();
        $cartStorage->expects($this->once())
            ->method('getCurrentCartIdentifier')
            ->will($this->returnValue(null))
        ;
        $cartStorage->expects($this->once())
            ->method('setCurrentCartIdentifier')
            ->with($this->equalTo($cart))
        ;

        $cartManager = $this->getMockCartManager();
        $cartManager->expects($this->once())
            ->method('createCart')
            ->will($this->returnValue($cart))
        ;
        $cartManager->expects($this->once())
            ->method('persistCart')
            ->with($this->equalTo($cart))
        ;

        $cartProvider = new CartProvider($cartStorage, $cartManager);

        $this->assertEquals($cart, $cartProvider->getCart());
    }

    public function testGetCartReturnsExistingCartBasedOnCartStorage()
    {
        $cartStorage = $this->getMockCartStorage();
        $cartStorage->expects($this->once())
            ->method('getCurrentCartIdentifier')
            ->will($this->returnValue(123))
        ;

        $cart = $this->getMockCart();

        $cartManager = $this->getMockCartManager();
        $cartManager->expects($this->once())
            ->method('findCart')
            ->with($this->equalTo(123))
            ->will($this->returnValue($cart))
        ;

        $cartProvider = new CartProvider($cartStorage, $cartManager);

        $this->assertEquals($cart, $cartProvider->getCart());
    }

    public function testSimpleGetSetCart()
    {
        $cart = $this->getMockCart();

        $cartProvider = new CartProvider($this->getMockCartStorage(), $this->getMockCartManager());
        $cartProvider->setCart($cart);

        $this->assertEquals($cart, $cartProvider->getCart());
    }

    private function getMockCartStorage()
    {
        return $this->getMock('Sylius\Bundle\CartBundle\Storage\CartStorageInterface');
    }

    private function getMockCartManager()
    {
        return $this->getMock('Sylius\Bundle\CartBundle\Model\CartManagerInterface');
    }

    private function getMockCart()
    {
        return $this->getMock('Sylius\Bundle\CartBundle\Model\CartInterface');
    }
}
