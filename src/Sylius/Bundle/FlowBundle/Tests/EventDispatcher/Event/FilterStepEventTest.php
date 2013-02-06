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
use Sylius\Bundle\FlowBundle\EventDispatcher\Event\FilterStepEvent;

/**
 * FilterStepEvent test.
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class FilterStepEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\EventDispatcher\Event\FilterStepEvent
     * @covers Sylius\Bundle\FlowBundle\EventDispatcher\SyliusFlowEvents
     */
    public function shouldDispatchFilterStepEvent()
    {
        $step = $this->getStep();
        $testCase = $this;
        $dispatcher = new EventDispatcher();

        $dispatcher->addListener('sylius.step.display', function (FilterStepEvent $event) use ($testCase, $step) {;
            $testCase->assertSame($step, $event->getStep());
        });

        $event = new FilterStepEvent($step);
        $dispatcher->dispatch(SyliusFlowEvents::STEP_DISPLAY, $event);
    }

    private function getStep()
    {
        return $this->getMock('Sylius\Bundle\FlowBundle\Process\Step\StepInterface');
    }
}
