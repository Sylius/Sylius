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
    /**
     * @test
     */
    public function shouldHaveCorrectDefaultValues()
    {
        $order = $this->getOrder();

        $this->assertFalse($order->isClosed());
        $this->assertTrue($order->isConfirmed());
    }

    private function getOrder()
    {
        return $this->getMockForAbstractClass('Sylius\Bundle\SalesBundle\Model\Order');
    }
}
