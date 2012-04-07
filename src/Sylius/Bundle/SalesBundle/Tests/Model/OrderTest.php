<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Tests\Model;

use Sylius\Bundle\SalesBundle\Model\Order;

/**
 * Order model test.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderTest extends \PHPUnit_Framework_TestCase
{
    public function testClosed()
    {
        $order = $this->getOrder();
        $this->assertFalse($order->isClosed());

        $order->setClosed(true);
        $this->assertTrue($order->isClosed());
        $order->setClosed(false);
        $this->assertFalse($order->isClosed());
    }

    public function testDefaultStatus()
    {
        $order = $this->getOrder();
        $this->assertEquals(0, $order->getStatus());

        return $order;
    }

    /**
     * @depends testDefaultStatus
     */
    public function testStatus($order)
    {
        $order->setStatus(1);
        $this->assertEquals(1, $order->getStatus());

        $order->setStatus(99);
        $this->assertEquals(99, $order->getStatus());
    }

    private function getOrder()
    {
        return $this->getMockForAbstractClass('Sylius\Bundle\SalesBundle\Model\Order');
    }
}
