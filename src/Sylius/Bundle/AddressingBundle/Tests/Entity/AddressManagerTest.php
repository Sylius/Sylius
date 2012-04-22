<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Tests\Entity;

use Sylius\Bundle\AddressingBundle\Entity\AddressManager;

/**
 * Address manager test.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class AddressManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testPersistAddress()
    {
        $address = $this->getMockAddress();

        $entityManager = $this->getMockEntityManager();
        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($address))
        ;
        $entityManager->expects($this->once())
            ->method('flush')
        ;

        $addressManager = new AddressManager($entityManager, 'Foo\\Bar');
        $addressManager->persistAddress($address);
    }

    public function testRemoveAddress()
    {
        $address = $this->getMockAddress();

        $entityManager = $this->getMockEntityManager();
        $entityManager->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($address))
        ;
        $entityManager->expects($this->once())
            ->method('flush')
        ;

        $addressManager = new AddressManager($entityManager, 'Foo\\Bar');
        $addressManager->removeAddress($address);
    }

    public function testFindAddress()
    {
        $address = $this->getMockAddress();

        $repository = $this->getMockEntityRepository('find', 3, $address);
        $entityManager = $this->getMockEntityManager($repository);

        $addressManager = new AddressManager($entityManager, 'Foo\\Bar');

        $this->assertEquals($address, $addressManager->findAddress(3));
    }

    public function testFindAddressBy()
    {
        $address = $this->getMockAddress();

        $repository = $this->getMockEntityRepository('findOneBy', array('firstname' => 'Paweł'), $address);
        $entityManager = $this->getMockEntityManager($repository);

        $addressManager = new AddressManager($entityManager, 'Foo\\Bar');

        $this->assertEquals($address, $addressManager->findAddressBy(array('firstname' => 'Paweł')));
    }

    public function testFindAddresses()
    {
        $result = array(
            $this->getMockAddress(),
            $this->getMockAddress(),
            $this->getMockAddress()
        );

        $repository = $this->getMockEntityRepository('findAll', null, $result);
        $entityManager = $this->getMockEntityManager($repository);

        $addressManager = new AddressManager($entityManager, 'Foo\\Bar');

        $this->assertEquals($result, $addressManager->findAddresses());
    }

    public function testFindAddressesBy()
    {
        $result = array(
            $this->getMockAddress()
        );

        $repository = $this->getMockEntityRepository('findBy', array('firstname' => 'Paweł'), $result);
        $entityManager = $this->getMockEntityManager($repository);

        $addressManager = new AddressManager($entityManager, 'Foo\\Bar');

        $this->assertEquals($result, $addressManager->findAddressesBy(array('firstname' => 'Paweł')));
    }

    private function getMockAddress()
    {
        return $this->getMock('Sylius\Bundle\AddressingBundle\Model\AddressInterface');
    }

    private function getMockEntityManager($repository = null)
    {
        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        if (null !== $repository) {
            $entityManager->expects($this->once())
                ->method('getRepository')
                ->will($this->returnValue($repository))
            ;
        }

        return $entityManager;
    }

    private function getMockEntityRepository($method, $criteria, $result)
    {
        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        if (null !== $criteria) {
            $repository->expects($this->once())
                ->method($method)
                ->with($this->equalTo($criteria))
                ->will($this->returnValue($result))
            ;
        } else {
            $repository->expects($this->once())
                ->method($method)
                ->will($this->returnValue($result))
            ;
        }

        return $repository;
    }
}
