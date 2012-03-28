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

use Sylius\Bundle\CartBundle\Entity\CartManager;

/**
 * Cart manager test for doctrine/orm driver.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testPersistCart()
    {
        $cart = $this->getMockCart();

        $entityManager = $this->getMockEntityManager();
        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($cart))
        ;
        $entityManager->expects($this->once())
            ->method('flush')
        ;

        $cartManager = new CartManager($entityManager, 'Foo\Bar');
        $cartManager->persistCart($cart);
    }

    public function testRemoveCart()
    {
        $cart = $this->getMockCart();

        $entityManager = $this->getMockEntityManager();
        $entityManager->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($cart))
        ;
        $entityManager->expects($this->once())
            ->method('flush')
        ;

        $cartManager = new CartManager($entityManager, 'Foo\Bar');
        $cartManager->removeCart($cart);
    }

    private function getMockCart()
    {
        return $this->getMock('Sylius\Bundle\CartBundle\Model\CartInterface');
    }

    private function getMockEntityManager()
    {
        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        return $entityManager;
    }
}
