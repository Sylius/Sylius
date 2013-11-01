<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Tests\Process\Context;

use Sylius\Bundle\FlowBundle\Storage\StorageInterface;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContext;
use Sylius\Bundle\FlowBundle\Validator\ProcessValidator;

/**
 * ProcessContext test.
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class ProcessContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider getMethodsWithoutInitialize
     * @expectedException RuntimeException
     */
    public function shouldNotExecuteMethodsWithoutContextInitialize($methodName)
    {
        $context = new ProcessContext($this->getMock('Sylius\Bundle\FlowBundle\Storage\StorageInterface'));
        $context->$methodName();
    }

    public function getMethodsWithoutInitialize()
    {
        return array(
            array('isValid'),
            array('getProcess'),
            array('getCurrentStep'),
            array('getPreviousStep'),
            array('getNextStep'),
            array('isFirstStep'),
            array('isLastStep'),
            array('close'),
            array('getProgress'),
            array('getProgress'),
        );
    }

    /**
     * @test
     */
    public function shouldInitializeStorage()
    {
        $storage = $this->getMock('Sylius\Bundle\FlowBundle\Storage\StorageInterface');
        $storage->expects($this->once())
            ->method('initialize')
            ->with($this->equalTo(md5('scenarioOne')));

        $context = new ProcessContext($storage);
        $context->initialize($this->getProcess(), $this->getStep('myStep'));
    }

    /**
     * @test
     */
    public function shouldSetPreviousStepWhenInitialize()
    {
        $steps = array(
            $this->getStep('step1'),
            $this->getStep('step2')
        );
        $process = $this->getProcess($steps);
        $context = new ProcessContext($this->getMock('Sylius\Bundle\FlowBundle\Storage\StorageInterface'));
        $context->initialize($process, $steps[1]);

        $this->assertEquals('step1', $context->getPreviousStep()->getName());
    }

    /**
     * @test
     */
    public function shouldSetNextStepWhenInitialize()
    {
        $steps = array(
            $this->getStep('step1'),
            $this->getStep('step2')
        );
        $process = $this->getProcess($steps);
        $context = new ProcessContext($this->getMock('Sylius\Bundle\FlowBundle\Storage\StorageInterface'));
        $context->initialize($process, $steps[0]);

        $this->assertEquals('step2', $context->getNextStep()->getName());
    }

    /**
     * @test
     */
    public function shouldSetCurrentStepWhenInitialize()
    {
        $steps = array(
            $this->getStep('step1'),
            $this->getStep('step2')
        );
        $process = $this->getProcess($steps);
        $context = new ProcessContext($this->getMock('Sylius\Bundle\FlowBundle\Storage\StorageInterface'));
        $context->initialize($process, $steps[0]);

        $this->assertSame($steps[0], $context->getCurrentStep());
    }

    /**
     * @test
     */
    public function shouldKnowWhenFirstStep()
    {
        $steps = array(
            $this->getStep('step1'),
            $this->getStep('step2')
        );
        $process = $this->getProcess($steps);

        $firstStepContext = new ProcessContext($this->getMock('Sylius\Bundle\FlowBundle\Storage\StorageInterface'));
        $firstStepContext->initialize($process, $steps[0]);
        $lastStepContext = new ProcessContext($this->getMock('Sylius\Bundle\FlowBundle\Storage\StorageInterface'));
        $lastStepContext->initialize($process, $steps[1]);

        $this->assertTrue($firstStepContext->isFirstStep());
        $this->assertFalse($lastStepContext->isFirstStep());
    }

    /**
     * @test
     */
    public function shouldClearStorageWhenClose()
    {
        $storage = $this->getMock('Sylius\Bundle\FlowBundle\Storage\StorageInterface');
        $storage->expects($this->once())
            ->method('clear');

        $context = new ProcessContext($storage);
        $context->initialize($this->getProcess(), $this->getStep('myStep'));
        $context->close();
    }

    /**
     * @test
     */
    public function shouldKnowWhenLastStep()
    {
        $steps = array(
            $this->getStep('step1'),
            $this->getStep('step2')
        );
        $process = $this->getProcess($steps);

        $firstStepContext = new ProcessContext($this->getMock('Sylius\Bundle\FlowBundle\Storage\StorageInterface'));
        $firstStepContext->initialize($process, $steps[0]);
        $lastStepContext = new ProcessContext($this->getMock('Sylius\Bundle\FlowBundle\Storage\StorageInterface'));
        $lastStepContext->initialize($process, $steps[1]);

        $this->assertFalse($firstStepContext->isLastStep());
        $this->assertTrue($lastStepContext->isLastStep());
    }

    /**
     * @test
     */
    public function shouldSetRequest()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');

        $context = new ProcessContext($this->getMock('Sylius\Bundle\FlowBundle\Storage\StorageInterface'));
        $context->setRequest($request);

        $this->assertSame($request, $context->getRequest());
    }

    /**
     * @test
     */
    public function shouldGetProcess()
    {
        $steps = array(
            $this->getStep('step1'),
            $this->getStep('step2')
        );
        $process = $this->getProcess($steps);

        $context = new ProcessContext($this->getMock('Sylius\Bundle\FlowBundle\Storage\StorageInterface'));
        $context->initialize($process, $steps[0]);

        $this->assertSame($process, $context->getProcess());
    }

    /**
     * @test
     * @expectedException RuntimeException
     */
    public function shouldNotBeValidWhenNotInitialized()
    {
        $context = new ProcessContext($this->getMock('Sylius\Bundle\FlowBundle\Storage\StorageInterface'));

        $context->isValid();
    }

    /**
     * @test
     */
    public function shouldNotBeValidWhenProcessValidatorIsNotValid()
    {
        $steps = array(
            $this->getStep('step1'),
            $this->getStep('step2')
        );
        $process = $this->getProcess($steps);
        $process->expects($this->once())
            ->method('getValidator')
            ->will($this->returnValue(new ProcessValidator(function () { return false; })));

        $context = new ProcessContext($this->getMock('Sylius\Bundle\FlowBundle\Storage\StorageInterface'));
        $context->initialize($process, $steps[0]);

        $this->assertTrue($context->isValid() !== true);
    }

    /**
     * @test
     */
    public function shouldNotBeValidWhenStepIsNotInHistory()
    {
        $steps = array(
            $this->getStep('step1'),
            $this->getStep('step2')
        );
        $process = $this->getProcess($steps);

        $storage = new TestArrayStorage();
        $history = array('step1');
        $storage->set('history', $history);

        $context = new ProcessContext($storage);
        $context->initialize($process, $steps[1]);

        $this->assertFalse($context->isValid());
    }

    /**
     * @test
     */
    public function shouldRewindHistory()
    {
        $steps = array(
            $this->getStep('step1'),
            $this->getStep('step2'),
        );
        $process = $this->getProcess($steps);

        $storage = new TestArrayStorage();
        $history = array('step1', 'step2');
        $storage->set('history', $history);

        $context = new ProcessContext($storage);
        $context->initialize($process, $steps[0]);

        $this->assertTrue($context->isValid());
        $context->rewindHistory();
        $this->assertCount(1, $storage->get('history'));
        $this->assertTrue(in_array('step1', $storage->get('history')));
        $this->assertFalse(in_array('step2', $storage->get('history')));
    }

    /**
     * @test
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function shouldFailToRewindHistory()
    {
        $steps = array(
            $this->getStep('step1'),
            $this->getStep('step2'),
        );
        $process = $this->getProcess($steps);

        $storage = new TestArrayStorage();
        $history = array('stepX', 'stepY');
        $storage->set('history', $history);

        $context = new ProcessContext($storage);
        $context->initialize($process, $steps[0]);

        $context->rewindHistory();
    }

    /**
     * @test
     */
    public function shouldBeValidWithEmptyHistory()
    {
        $steps = array(
            $this->getStep('step1'),
            $this->getStep('step2')
        );
        $process = $this->getProcess($steps);

        $context = new ProcessContext(new TestArrayStorage());
        $context->initialize($process, $steps[0]);

        $this->assertTrue($context->isValid());
    }

    /**
     * @test
     */
    public function shouldBeValidWithHistory()
    {
        $steps = array(
            $this->getStep('step1'),
            $this->getStep('step2')
        );
        $process = $this->getProcess($steps);

        $storage = new TestArrayStorage();
        $history = array('step1', 'step2');
        $storage->set('history', $history);
        $context = new ProcessContext($storage);
        $context->initialize($process, $steps[0]);

        $this->assertTrue($context->isValid());
    }

    /**
     * @test
     */
    public function shouldBeValidWithoutHistory()
    {
        $process = $this->getProcess(array());

        $context = new ProcessContext($this->getMock('Sylius\Bundle\FlowBundle\Storage\StorageInterface'));
        $context->initialize($process, $this->getStep('someStep'));

        $this->assertTrue($context->isValid());
    }

    /**
     * @test
     * @dataProvider getProgressData
     */
    public function shouldCalculateProgress($steps, $index, $expectedProgress)
    {
        $process = $this->getProcess($steps);

        $context = new ProcessContext($this->getMock('Sylius\Bundle\FlowBundle\Storage\StorageInterface'));
        $context->initialize($process, $steps[$index]);

        $this->assertEquals($context->getProgress(), $expectedProgress);
    }

    public function getProgressData()
    {
        return array(
            array(
                array(
                    $this->getStep('step1'),
                    $this->getStep('step2')
                ),
                0,
                50
            ),
            array(
                array(
                    $this->getStep('step1'),
                    $this->getStep('step2')
                ),
                1,
                100
            ),
            array(
                array(
                    $this->getStep('step1'),
                    $this->getStep('step2'),
                    $this->getStep('step3')
                ),
                0,
                33
            ),
            array(
                array(
                    $this->getStep('step1'),
                    $this->getStep('step2'),
                    $this->getStep('step3')
                ),
                1,
                66
            ),
        );
    }

    /**
     * @test
     */
    public function shouldInjectStorageBySetter()
    {
        $storage1 = $this->getMock('Sylius\Bundle\FlowBundle\Storage\StorageInterface');
        $storage2 = $this->getMock('Sylius\Bundle\FlowBundle\Storage\StorageInterface');

        $context = new ProcessContext($storage1);
        $context->setStorage($storage2);

        $this->assertSame($storage2, $context->getStorage());
    }

    private function getProcess($steps = array())
    {
        $process = $this->getMock('Sylius\Bundle\FlowBundle\Process\ProcessInterface');
        $process->expects($this->any())
            ->method('setScenarioAlias')
            ->with($this->equalTo('scenarioOne'));
        $process->expects($this->any())
            ->method('getScenarioAlias')
            ->will($this->returnValue('scenarioOne'));
        $process->expects($this->any())
            ->method('getOrderedSteps')
            ->will($this->returnValue($steps));
        $process->expects($this->any())
            ->method('countSteps')
            ->will($this->returnValue(count($steps)));

        return $process;
    }

    private function getStep($name)
    {
        $step = $this->getMock('Sylius\Bundle\FlowBundle\Process\Step\StepInterface');
        $step->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));
        $step->expects($this->any())
            ->method('displayAction')
            ->will($this->returnValue('displayActionResponse'));
        $step->expects($this->any())
            ->method('forwardAction')
            ->will($this->returnValue('forwardActionResponse'));

        return $step;
    }
}

class TestArrayStorage implements StorageInterface
{
    private $data = array();

    public function initialize($domain)
    {

    }

    public function has($key)
    {
        return isset($this->data[$key]);
    }

    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->data[$key] : $default;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function remove($key)
    {
        unset($this->data[$key]);
    }

    public function clear()
    {
        $this->data = array();
    }
}
