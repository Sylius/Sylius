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
use Sylius\Bundle\FlowBundle\Process\Step\AbstractStep;
use Sylius\Bundle\FlowBundle\Validator\ProcessValidator;

/**
 * Process test.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class ProcessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldKeepStepsInOrderWhileAddingSteps()
    {
        $process = new Process();

        $process->addStep('foo', new TestAbstractStep());
        $process->addStep('bar', new TestAbstractStep());
        $process->addStep('foobar', new TestAbstractStep());

        $correctOrder = array('foo', 'bar', 'foobar');

        foreach ($process->getOrderedSteps() as $i => $step) {
            $this->assertSame($correctOrder[$i], $step->getName());
        }

        foreach ($correctOrder as $i => $name) {
            $this->assertSame($name, $process->getStepByIndex($i)->getName());
        }
    }

    /**
     * @test
     */
    public function shouldKeepStepsInOrderAfterSetSteps()
    {
        $process = new Process();

        $process->setSteps(array(
            'foo' => new TestAbstractStep(),
            'bar' => new TestAbstractStep(),
            'foobar' => new TestAbstractStep()
        ));

        $correctOrder = array('foo', 'bar', 'foobar');

        foreach ($process->getOrderedSteps() as $i => $step) {
            $this->assertSame($correctOrder[$i], $step->getName());
        }

        foreach ($correctOrder as $i => $name) {
            $this->assertSame($name, $process->getStepByIndex($i)->getName());
        }
    }

    /**
     * @test
     */
    public function shouldKeepStepsInSequentialOrderAfterRemoveStep()
    {
        $process = new Process();

        $process->setSteps(array(
            'foo' => new TestAbstractStep(),
            'bar' => new TestAbstractStep(),
            'foobar' => new TestAbstractStep()
        ));

        $process->removeStep('bar');
        $process->addStep('bar', new TestAbstractStep());

        $correctOrder = array('foo', 'foobar', 'bar');

        foreach ($process->getOrderedSteps() as $i => $step) {
            $this->assertSame($correctOrder[$i], $step->getName());
        }

        foreach ($correctOrder as $i => $name) {
            $this->assertSame($name, $process->getStepByIndex($i)->getName());
        }
    }

    /**
     * @test
     */
    public function shouldAddStep()
    {
        $process = new Process();
        $step1 = new TestAbstractStep();
        $process->addStep('foo', $step1);

        $steps = $process->getSteps();
        $this->assertSame($step1, $steps['foo']);
    }

    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\Process\Process::removeStep
     */
    public function shouldRemoveStep()
    {
        $process = new Process();
        $process->addStep('foo', new TestAbstractStep());
        $process->removeStep('foo');

        $this->assertCount(0, $process->getSteps());
    }

    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\Process\Process::removeStep
     * @expectedException \InvalidArgumentException
     */
    public function shouldNotRemoveStepWhenWasNotSet()
    {
        $process = new Process();
        $process->removeStep('foo');
    }

    /**
     * @test
     */
    public function shouldSetSteps()
    {
        $process = new Process();
        $step1 = new TestAbstractStep();
        $process->setSteps(array('foo' => $step1));

        $steps = $process->getSteps();
        $this->assertSame($step1, $steps['foo']);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function shouldNotAddStepWithThisSameNameAgain()
    {
        $process = new Process();

        $process->addStep('foo', new TestAbstractStep());
        $process->addStep('foo', new TestAbstractStep());
    }

    /**
     * @test
     */
    public function shouldGetStepUsingIndexAfterSetSteps()
    {
        $process = new Process();

        $step1 = new TestAbstractStep();
        $step2 = new TestAbstractStep();

        $process->setSteps(array(
            'foo' => $step1,
            'bar' => $step2,
        ));

        $this->assertSame($step1, $process->getStepByIndex(0));
        $this->assertSame($step2, $process->getStepByIndex(1));
    }

    /**
     * @test
     */
    public function shouldGetStepUsingIndexAfterStepAddition()
    {
        $process = new Process();

        $step1 = new TestAbstractStep();
        $step2 = new TestAbstractStep();

        $process->addStep('foo', $step1);
        $process->addStep('bar', $step2);

        $this->assertSame($step1, $process->getStepByIndex(0));
        $this->assertSame($step2, $process->getStepByIndex(1));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function shouldNotGetStepUsingIndexWhenWasNotSet()
    {
        $process = new Process();
        $process->getStepByIndex(0);
    }

    /**
     * @test
     */
    public function shouldGetStepUsingNameAfterSetSteps()
    {
        $process = new Process();

        $step1 = new TestAbstractStep();
        $step2 = new TestAbstractStep();

        $process->setSteps(array(
            'foo' => $step1,
            'bar' => $step2,
        ));

        $this->assertSame($step1, $process->getStepByName('foo'));
        $this->assertSame($step2, $process->getStepByName('bar'));
    }

    /**
     * @test
     */
    public function shouldGetStepUsingNameAfterStepAddition()
    {
        $process = new Process();

        $step1 = new TestAbstractStep();
        $step2 = new TestAbstractStep();

        $process->addStep('foo', $step1);
        $process->addStep('bar', $step2);

        $this->assertSame($step1, $process->getStepByName('foo'));
        $this->assertSame($step2, $process->getStepByName('bar'));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function shouldNotGetStepUsingNameWhenWasNotSet()
    {
        $process = new Process();
        $process->getStepByName('foo');
    }

    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\Process\Process::getLastStep
     */
    public function shouldGetLastStep()
    {
        $process = new Process();

        $step1 = new TestAbstractStep();
        $step2 = new TestAbstractStep();

        $process->addStep('foo', $step1);
        $process->addStep('bar', $step2);

        $this->assertSame($step2, $process->getLastStep());
    }

    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\Process\Process::getFirstStep
     */
    public function shouldGetFirstStep()
    {
        $process = new Process();

        $step1 = new TestAbstractStep();
        $step2 = new TestAbstractStep();

        $process->addStep('foo', $step1);
        $process->addStep('bar', $step2);

        $this->assertSame($step1, $process->getFirstStep());
    }

    /**
     * @test
     */
    public function shouldSetNeededDataUsingSetter()
    {
        $process = new Process();
        $process->setScenarioAlias('alias');
        $process->setDisplayRoute('displayRoute');
        $process->setDisplayRouteParams(array('foo' => 'bar'));
        $process->setForwardRoute('forwardRoute');
        $process->setForwardRouteParams(array('foo' => 'bar'));
        $process->setRedirect('http://somepage');
        $process->setRedirectParams(array('foo' => 'bar'));
        $process->setValidator(new ProcessValidator('An error occurred.', null, function () {
            return false;
        }));

        $validator = $process->getValidator();
        $this->assertSame('alias', $process->getScenarioAlias());
        $this->assertSame('displayRoute', $process->getDisplayRoute());
        $this->assertSame(array('foo' => 'bar'), $process->getDisplayRouteParams());
        $this->assertSame('forwardRoute', $process->getForwardRoute());
        $this->assertSame(array('foo' => 'bar'), $process->getForwardRouteParams());
        $this->assertSame('http://somepage', $process->getRedirect());
        $this->assertSame(array('foo' => 'bar'), $process->getRedirectParams());
        $this->assertSame(false, $validator->isValid());
    }

    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\Process\Process::countSteps
     * @dataProvider countStepsDataProvider
     */
    public function shouldCountSteps($steps, $expectedCount)
    {
        $process = new Process();

        $process->setSteps($steps);

        $this->assertEquals($process->countSteps(), $expectedCount);
    }

    public function countStepsDataProvider()
    {
        return array(
            array(
                array(new TestAbstractStep(), new TestAbstractStep()),
                2
            ),
            array(
                array('abc' => new TestAbstractStep(), 'abc' => new TestAbstractStep()),
                1
            ),
            array(
                array('abc' => new TestAbstractStep()),
                1
            ),
            array(
                array('abc' => new TestAbstractStep(), 'zzz' => new TestAbstractStep(), 'yyy' => new TestAbstractStep()),
                3
            ),
        );
    }
}

class TestAbstractStep extends AbstractStep
{
    public function displayAction(ProcessContextInterface $context)
    {
        // pufff.
    }
}
