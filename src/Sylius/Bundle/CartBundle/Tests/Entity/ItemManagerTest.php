<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Tests\Entity;

use Sylius\Bundle\CartBundle\Entity\ItemManager;

/**
 * Item manager test for doctrine/orm driver.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ItemManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testPersistItem()
    {
        $item = $this->getMockItem();

        $entityManager = $this->getMockEntityManager();
        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($item))
        ;
        $entityManager->expects($this->once())
            ->method('flush')
        ;

        $itemManager = new ItemManager($entityManager, 'Foo\Bar');
        $itemManager->persistItem($item);
    }

    public function testRemoveItem()
    {
        $item = $this->getMockItem();

        $entityManager = $this->getMockEntityManager();
        $entityManager->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($item))
        ;
        $entityManager->expects($this->once())
            ->method('flush')
        ;

        $itemManager = new ItemManager($entityManager, 'Foo\Bar');
        $itemManager->removeItem($item);
    }

    private function getMockItem()
    {
        return $this->getMock('Sylius\Bundle\CartBundle\Model\ItemInterface');
    }

    private function getMockEntityManager()
    {
        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue(null))
        ;

        return $entityManager;
    }

}
