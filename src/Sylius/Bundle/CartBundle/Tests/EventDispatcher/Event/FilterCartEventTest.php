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

use Sylius\Bundle\CartBundle\EventDispatcher\Event\FilterCartEvent;

/**
 * Cart filtering event test.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FilterCartEventTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $category = $this->getMock('Sylius\Bundle\CartBundle\Model\CartInterface');

        $event = new FilterCartEvent($category);

        $this->assertEquals($category, $event->getCart());
    }
}
