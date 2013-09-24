<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Tests\EventDispatcher\Event;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Sylius\Bundle\FlowBundle\EventDispatcher\SyliusFlowEvents;
use Sylius\Bundle\FlowBundle\EventDispatcher\Event\FilterProcessEvent;

/**
 * FilterProcessEvent test.
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class FilterProcessEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\EventDispatcher\Event\FilterProcessEvent
     * @covers Sylius\Bundle\FlowBundle\EventDispatcher\SyliusFlowEvents
     */
    public function shouldDispatchFilterProcessEvent()
    {
        $process = $this->getProcess();
        $testCase = $this;
        $dispatcher = new EventDispatcher();

        $dispatcher->addListener('sylius.process.start', function (FilterProcessEvent $event) use ($testCase, $process) {
            $testCase->assertSame($process, $event->getProcess());
        });

        $event = new FilterProcessEvent($process);
        $dispatcher->dispatch(SyliusFlowEvents::PROCESS_START, $event);
    }

    private function getProcess()
    {
        return $this->getMock('Sylius\Bundle\FlowBundle\Process\ProcessInterface');
    }
}
