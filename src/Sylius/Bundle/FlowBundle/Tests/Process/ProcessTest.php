<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Tests\Process;

use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\FlowBundle\Process\Process;
use Sylius\Bundle\FlowBundle\Process\Step\Step;

/**
 * Process test.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProcessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldKeepStepsInOrderWhileAddingSteps()
    {
        $process = new Process();

        $process->addStep('foo', new TestStep());
        $process->addStep('bar', new TestStep());
        $process->addStep('foobar', new TestStep());

        $correctOrder = array('foo', 'bar', 'foobar');

        foreach ($process->getOrderedSteps() as $i => $step) {
            $this->assertSame($correctOrder[$i], $step->getName());
        }

        foreach ($correctOrder as $i => $name) {
            $this->assertSame($name, $process->getStepByIndex($i)->getName());
        }
    }
}

class TestStep extends Step
{
    public function displayAction(ProcessContextInterface $context)
    {
        // pufff.
    }
}
