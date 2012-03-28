<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Tests\EventDispatcher\Event;

use Sylius\Bundle\CartBundle\EventDispatcher\Event\CartOperationEvent;

/**
 * Cart operation event test.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartOperationEventTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $cart = $this->getMock('Sylius\Bundle\CartBundle\Model\CartInterface');
        $item = $this->getMock('Sylius\Bundle\CartBundle\Model\ItemInterface');

        $event = new CartOperationEvent($cart, $item);

        $this->assertEquals($cart, $event->getCart());
        $this->assertEquals($item, $event->getItem());
    }
}
