<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Tests\Manipulator;

use Sylius\Bundle\SalesBundle\Manipulator\OrderManipulator;

/**
 * Order manipulator test.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderManipulatorTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatePersistsOrder()
    {
        $order = $this->getMockOrder();

        $orderManager = $this->getMockOrderManager();
        $orderManager->expects($this->once())
            ->method('persistOrder')
            ->with($this->equalTo($order))
        ;

        $manipulator = new OrderManipulator($orderManager, $this->getMockSlugizer());
        $manipulator->create($order);
    }

    public function testUpdatePersistsOrder()
    {
        $order = $this->getMockOrder();

        $orderManager = $this->getMockOrderManager();
        $orderManager->expects($this->once())
            ->method('persistOrder')
            ->with($this->equalTo($order))
        ;

        $manipulator = new OrderManipulator($orderManager, $this->getMockSlugizer());
        $manipulator->update($order);
    }

    public function testDeleteRemovesOrder()
    {
        $order = $this->getMockOrder();

        $orderManager = $this->getMockOrderManager();
        $orderManager->expects($this->once())
            ->method('removeOrder')
            ->with($this->equalTo($order))
        ;

        $manipulator = new OrderManipulator($orderManager, $slugizer = $this->getMockSlugizer());
        $manipulator->delete($order);
    }

    private function getMockOrder()
    {
        return $this->getMock('Sylius\Bundle\SalesBundle\Model\OrderInterface');
    }

    private function getMockOrderManager()
    {
        $orderManager = $this->getMockBuilder('Sylius\Bundle\SalesBundle\Model\OrderManagerInterface')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $orderManager->expects($this->any())
            ->method('persistOrder')
            ->will($this->returnValue(null))
        ;

        return $orderManager;
    }

    private function getMockSlugizer()
    {
        return $this->getMock('Sylius\Bundle\SalesBundle\Inflector\SlugizerInterface');
    }
}
