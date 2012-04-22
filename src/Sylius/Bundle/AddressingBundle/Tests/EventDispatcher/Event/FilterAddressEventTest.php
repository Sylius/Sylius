<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Tests\EventDispatcher\Event;

use Sylius\Bundle\AddressingBundle\EventDispatcher\Event\FilterAddressEvent;

/**
 * Filter address event test.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FilterAddressEventTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $address = $this->getMockAddress();
        $event = new FilterAddressEvent($address);
        $this->assertEquals($address, $event->getAddress());
    }

    private function getMockAddress()
    {
        return $this->getMock('Sylius\Bundle\AddressingBundle\Model\AddressInterface');
    }
}
