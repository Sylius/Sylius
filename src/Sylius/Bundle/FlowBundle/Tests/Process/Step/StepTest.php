<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Tests\Process\Step;

use Sylius\Bundle\FlowBundle\Process\Step\ActionResult;

/**
 * Step test.
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class StepTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\Process\Step\Step::isActive
     */
    public function shouldByActiveByDefault()
    {
        $step = $this->getStep();

        $this->assertTrue($step->isActive());
    }

    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\Process\Step\Step::forwardAction
     */
    public function shouldCompleteProcessByDefault()
    {
        $processContext = $this->getMock('Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface');

        $step = $this->getStep();
        /** @var $result ActionResult */
        $result = $step->forwardAction($processContext);
        $this->assertEmpty($result->getNextStepName());
    }

    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\Process\Step\Step::setName
     * @covers Sylius\Bundle\FlowBundle\Process\Step\Step::getName
     */
    public function shouldSetName()
    {
        $step = $this->getStep();
        $step->setName('stepName');

        $this->assertSame('stepName', $step->getName());
    }

    private function getStep()
    {
        return $this->getMockForAbstractClass('Sylius\Bundle\FlowBundle\Process\Step\Step');
    }
}
