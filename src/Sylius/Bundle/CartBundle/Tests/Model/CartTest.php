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
 * Cart model test.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $cart = $this->getCart();

        $this->assertTrue($cart->isEmpty());
        $this->assertFalse($cart->isLocked());
        $this->assertInstanceOf('DateTime', $cart->getExpiresAt());
    }

    public function testGetSetId()
    {
        $cart = $this->getCart();
        $this->assertNull($cart->getId());

        $cart->setId(5);
        $this->assertEquals(5, $cart->getId());
    }

    public function testGetSetTotalItems()
    {
        $cart = $this->getCart();

        $cart->setTotalItems(3);
        $this->assertEquals(3, $cart->getTotalItems());
    }

    public function testIsSetLocked()
    {
        $cart = $this->getCart();

        $cart->setLocked(true);
        $this->assertTrue($cart->isLocked());
    }

    public function testGetSetExpiresAt()
    {
        $cart = $this->getCart();
        $expiresAt = $cart->getExpiresAt();

        sleep(1);

        $this->assertEquals($expiresAt, $cart->getExpiresAt());
    }

    public function testIncrementExpiresAt()
    {
        $cart = $this->getCart();
        $expiresAt = $cart->getExpiresAt();

        sleep(1);
        $cart->incrementExpiresAt();

        $this->assertGreaterThan($expiresAt, $cart->getExpiresAt());
    }

    /**
     * @return \Sylius\Bundle\CartBundle\Model\Cart
     */
    private function getCart()
    {
        return $this->getMockForAbstractClass('Sylius\Bundle\CartBundle\Model\Cart');
    }
}
