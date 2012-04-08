<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Tests\Entity;

use Sylius\Bundle\SalesBundle\Entity\OrderManager;

/**
 * Order manager test for doctrine/orm driver.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testPersistOrder()
    {
        $order = $this->getMockOrder();

        $entityManager = $this->getMockEntityManager();
        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($order))
        ;
        $entityManager->expects($this->once())
            ->method('flush')
        ;

        $orderManager = new OrderManager($entityManager, 'Foo\Bar');
        $orderManager->persistOrder($order);
    }

    public function testRemoveOrder()
    {
        $order = $this->getMockOrder();

        $entityManager = $this->getMockEntityManager();
        $entityManager->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($order))
        ;
        $entityManager->expects($this->once())
            ->method('flush')
        ;

        $orderManager = new OrderManager($entityManager, 'Foo\Bar');
        $orderManager->removeOrder($order);
    }

    private function getMockOrder()
    {
        return $this->getMock('Sylius\Bundle\SalesBundle\Model\OrderInterface');
    }

    private function getMockEntityManager($repoResult = array(), $findParams = array())
    {
        return $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

}

