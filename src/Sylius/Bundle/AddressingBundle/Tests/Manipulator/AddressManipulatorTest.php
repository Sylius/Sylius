<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Tests\Manipulator;

use Sylius\Bundle\AddressingBundle\Manipulator\AddressManipulator;

/**
 * Address manipulator test.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class AddressManipulatorTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatePersistsAddress()
    {
        $address = $this->getMockAddress();

        $addressManager = $this->getMockAddressManager();
        $addressManager->expects($this->once())
            ->method('persistAddress')
            ->with($this->equalTo($address))
        ;

        $manipulator = new AddressManipulator($addressManager);
        $manipulator->create($address);
    }

    public function testUpdatePersistsAddress()
    {
        $address = $this->getMockAddress();

        $addressManager = $this->getMockAddressManager();
        $addressManager->expects($this->once())
            ->method('persistAddress')
            ->with($this->equalTo($address))
        ;

        $manipulator = new AddressManipulator($addressManager);
        $manipulator->update($address);
    }

    public function testDeleteRemovesAddress()
    {
        $address = $this->getMockAddress();

        $addressManager = $this->getMockAddressManager();
        $addressManager->expects($this->once())
            ->method('removeAddress')
            ->with($this->equalTo($address))
        ;

        $manipulator = new AddressManipulator($addressManager);
        $manipulator->delete($address);
    }

    private function getMockAddress()
    {
        return $this->getMock('Sylius\Bundle\AddressingBundle\Model\AddressInterface');
    }

    private function getMockAddressManager()
    {
        $addressManager = $this->getMockBuilder('Sylius\Bundle\AddressingBundle\Model\AddressManagerInterface')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        return $addressManager;
    }
}
