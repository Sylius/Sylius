<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Tests\Storage;

use Sylius\Bundle\CartBundle\Storage\SessionCartStorage;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * Session storage test.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SessionCartStorageTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCurrentCartIdentifier()
    {
        $session = $this->getMockSession();
        $session->expects($this->once())
            ->method('get')
            ->will($this->returnValue(123))
        ;

        $storage = new SessionCartStorage($session);

        $this->assertEquals(123, $storage->getCurrentCartIdentifier());
    }

    public function testSetCurrentCartIdentifier()
    {
        $cart = $this->getMockCart();
        $cart->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(123))
        ;

        $storage = new SessionCartStorage($this->getSession());
        $storage->setCurrentCartIdentifier($cart);

        $this->assertEquals(123, $storage->getCurrentCartIdentifier());

        return $storage;
    }

    /**
     * @depends testSetCurrentCartIdentifier
     */
    public function testResetCurrentCartIdentifier(SessionCartStorage $storage)
    {
        $storage->resetCurrentCartIdentifier();

        $this->assertNull($storage->getCurrentCartIdentifier());
    }

    private function getSession()
    {
        return new Session(new MockArraySessionStorage());
    }

    private function getMockSession()
    {
        return $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
    }

    private function getMockCart()
    {
        return $this->getMock('Sylius\Bundle\CartBundle\Model\CartInterface');
    }
}

