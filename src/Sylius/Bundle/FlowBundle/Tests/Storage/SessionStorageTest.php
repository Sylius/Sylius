<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Tests\Storage;

use Sylius\Bundle\FlowBundle\Storage\SessionFlowsBag;
use Sylius\Bundle\FlowBundle\Storage\SessionStorage;

/**
 * SessionStorage test.
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class SessionStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\Storage\SessionStorage
     */
    public function shouldSetValueToSessionBag()
    {
        $sessionBag = $this->getSessionBag();
        $sessionBag->expects($this->once())
            ->method('set')
            ->with('mydomain/test', 'my-value');
        $sessionBag->expects($this->once())
            ->method('get')
            ->with('mydomain/test')
            ->will($this->returnValue('my-value'));

        $sessionStorage = new SessionStorage($this->getSession($sessionBag));
        $sessionStorage->initialize('mydomain');
        $sessionStorage->set('test', 'my-value');

        $this->assertEquals('my-value', $sessionStorage->get('test'));
    }

    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\Storage\SessionStorage
     */
    public function shouldCheckIfValueIsSetInSessionBag()
    {
        $sessionBag = $this->getSessionBag();
        $sessionBag->expects($this->once())
            ->method('has')
            ->with('mydomain/test')
            ->will($this->returnValue(true));

        $sessionStorage = new SessionStorage($this->getSession($sessionBag));
        $sessionStorage->initialize('mydomain');

        $this->assertTrue($sessionStorage->has('test'));
    }

    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\Storage\SessionStorage
     */
    public function shouldRemoveFromSessionBag()
    {
        $sessionBag = $this->getSessionBag();
        $sessionBag->expects($this->once())
            ->method('remove')
            ->with('mydomain/test');

        $sessionStorage = new SessionStorage($this->getSession($sessionBag));
        $sessionStorage->initialize('mydomain');

        $sessionStorage->remove('test');
    }

    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\Storage\SessionStorage
     */
    public function shouldClearDomainInSessionBag()
    {
        $sessionBag = $this->getSessionBag();
        $sessionBag->expects($this->once())
            ->method('remove')
            ->with('mydomain');

        $sessionStorage = new SessionStorage($this->getSession($sessionBag));
        $sessionStorage->initialize('mydomain');

        $sessionStorage->clear();
    }

    private function getSessionBag()
    {
        return $this->getMock('Sylius\Bundle\FlowBundle\Storage\SessionFlowsBag');
    }

    private function getSession($bag)
    {
        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $session->expects($this->any())
            ->method('getBag')
            ->will($this->returnValue($bag));

        return $session;
    }
}
