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
        $cart = $this->getMockCart();
        $cart->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(123))
        ;

        $storage = new SessionCartStorage($this->getSession());
        $storage->setCurrentCartIdentifier($cart);

        $this->assertEquals(123, $storage->getCurrentCartIdentifier());
    }

    private function getSession()
    {
        return new Session(new MockArraySessionStorage());
    }

    private function getMockCart()
    {
        return $this->getMock('Sylius\Bundle\CartBundle\Model\CartInterface');
    }
}

