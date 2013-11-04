<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Tests\Process\Builder;

use Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilder;

/**
 * ProcessBuilder test.
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class ProcessBuilderTest extends \PHPUnit_Framework_TestCase
{
    private $builder;

    public function setUp()
    {
        $this->builder = new TestProcessBuilder($this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'));
    }

    /**
     * @test
     */
    public function shouldCreateProcess()
    {
        $process = $this->builder->build($this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));

        $this->assertInstanceOf('Sylius\Bundle\FlowBundle\Process\Process', $process);
    }

    /**
     * @test
     */
    public function shouldBuildScenario()
    {
        $scenario = $this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface');
        $scenario->expects($this->once())
            ->method('build')
            ->with($this->equalTo($this->builder));

        $this->builder->build($scenario);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function shouldNotAddWithoutProcess()
    {
        $process = $this->getMock('Sylius\Bundle\FlowBundle\Process\ProcessInterface');

        $this->builder->registerStep('new', $this->getStep('somename'));
        $this->builder->add('somename', 'new');
    }

    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilder::add
     */
    public function shouldInjectContainerToContainerAwareStep()
    {
        $step = $this->getMock('Sylius\Bundle\FlowBundle\Process\Step\ContainerAwareStep');
        $step->expects($this->once())
            ->method('setContainer')
            ->with($this->isInstanceOf('Symfony\Component\DependencyInjection\ContainerInterface'));

        $this->builder->build($this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));
        $this->builder->add('somename', $step);
    }

    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilder::add
     * @covers Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilder::registerStep
     */
    public function shouldAcceptStepAliasWhileAdding()
    {
        $step = $this->getStep();
        $step->expects($this->any())
            ->method('setName')
            ->with($this->equalTo('somename'));

        $this->builder->build($this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));
        $this->builder->registerStep('new', $step);
        $this->builder->add('somename', 'new');

        $this->assertSame($step, $this->builder->getProcess()->getStepByName('somename'));
        $this->assertCount(1, $this->builder->getProcess()->getSteps());
    }

    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilder::add
     * @expectedException InvalidArgumentException
     */
    public function shouldNotAddObjectWhichAreNotSteps()
    {
        $this->builder->build($this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));
        $this->builder->add('some', new \stdClass);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function shouldNotRemoveStepWithoutProcess()
    {
        $this->builder->remove('test');
    }

    /**
     * @test
     */
    public function shouldRemoveStepFromProcess()
    {
        $this->builder->build($this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));
        $this->builder->add('some', $this->getStep('some'));
        $this->builder->remove('some');

        $this->assertCount(0, $this->builder->getProcess()->getSteps());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function shouldNotCheckIfStepIsSetWithoutProcess()
    {
        $this->builder->has('test');
    }

    /**
     * @test
     */
    public function shouldCheckIfStepIsSet()
    {
        $this->builder->build($this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));

        $this->assertFalse($this->builder->has('some'));
        $this->builder->add('some', $this->getStep('some'));
        $this->assertTrue($this->builder->has('some'));
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function shouldNotInjectDisplayRouteWithoutProcess()
    {
        $this->builder->setDisplayRoute('display_route');
    }

    /**
     * @test
     */
    public function shouldInjectDisplayRouteToProcess()
    {
        $this->builder->build($this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));
        $this->builder->setDisplayRoute('display_route');

        $this->assertEquals('display_route', $this->builder->getProcess()->getDisplayRoute());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function shouldNotInjectForwardRouteWithoutProcess()
    {
        $this->builder->setForwardRoute('forward_route');
    }

    /**
     * @test
     */
    public function shouldInjectForwardRouteToProcess()
    {
        $this->builder->build($this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));
        $this->builder->setForwardRoute('forward_route');

        $this->assertEquals('forward_route', $this->builder->getProcess()->getForwardRoute());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function shouldNotInjectRedirectWithoutProcess()
    {
        $this->builder->setRedirect('redirect');
    }

    /**
     * @test
     */
    public function shouldInjectRedirectToProcess()
    {
        $this->builder->build($this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));
        $this->builder->setRedirect('redirect');

        $this->assertEquals('redirect', $this->builder->getProcess()->getRedirect());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function shouldNotInjectValidationClosureWithoutProcess()
    {
        $this->builder->validate(function () {
            return 'my-closure';
        });
    }

    /**
     * @test
     */
    public function shouldInjectValidationClosureToProcess()
    {
        $this->builder->build($this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));
        $this->builder->validate(function () {
            return false;
        });

        $validator = $this->builder->getProcess()->getValidator();
        $this->assertEquals(false, $validator->isValid());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function shouldNotRegisterTwoThisSameSteps()
    {
        $this->builder->registerStep('new', $this->getStep('somename'));
        $this->builder->registerStep('new', $this->getStep('somename'));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function shouldNotLoadStepWhenWasNotRegisteredBefore()
    {
        $this->builder->loadStep('new');
    }

    /**
     * @test
     */
    public function shouldLoadStep()
    {
        $step = $this->getStep('somename');
        $this->builder->registerStep('new', $step);

        $this->assertSame($this->builder->loadStep('new'), $step);
    }

    private function getStep($name = '')
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

class TestProcessBuilder extends ProcessBuilder
{
    /**
     * Method getProcess exists only in TestProcessBuilder to allow testing
     */
    public function getProcess()
    {
        return $this->process;
    }
}
