<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Tests\EventDispatcher\Event;

use Sylius\Bundle\SalesBundle\EventDispatcher\Event\FilterOrderEvent;

/**
 * Order filtering event test.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FilterOrderEventTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $order = $this->getMock('Sylius\Bundle\SalesBundle\Model\OrderInterface');

        $event = new FilterOrderEvent($order);

        $this->assertEquals($order, $event->getOrder());
    }
}
