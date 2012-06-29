<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Tests\Process\Coordinator;

use Sylius\Bundle\FlowBundle\Process\Coordinator\Coordinator;

/**
 * Coordinator test.
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class CoordinatorTest extends \PHPUnit_Framework_TestCase
{
    private $coordinator;

    public function setUp()
    {
        $router = $this->getRouter(
            'sylius_flow_display',
             array(
                'scenarioAlias' => 'scenarioOne',
                'stepName' => 'firstStepName'
            ),
            'http://someurl.dev/step/scenarioOne/firstStepName'
        );

        $processBuilder = $this->getProcessBuilder($this->getProcess());

        $processContext = $this->getProcessContext();
        $processContext->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->coordinator = $this->createCoordinator($router, $processBuilder, $processContext);
    }

    /**
     * @test
     */
    public function shouldRedirectToDefaultDisplayActionWhenStarting()
    {
        $this->coordinator->registerScenario('scenarioOne', $this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));

        $response = $this->coordinator->start('scenarioOne');

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals('http://someurl.dev/step/scenarioOne/firstStepName', $response->getTargetUrl());
    }

    /**
     * @test
     */
    public function shouldRedirectToMyRouteDisplayActionWhenStarting()
    {
        $router = $this->getRouter(
            'my_route',
             array(
                'stepName' => 'firstStepName'
            ),
            'http://someurl.dev/my-super-route/firstStepName'
        );

        $process = $this->getProcess();
        $process->expects($this->any())
            ->method('getDisplayRoute')
            ->will($this->returnValue('my_route'));

        $processBuilder = $this->getProcessBuilder($process);

        $processContext = $this->getProcessContext();
        $processContext->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->coordinator = $this->createCoordinator($router, $processBuilder, $processContext);
        $this->coordinator->registerScenario('scenarioOne', $this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));
        $response = $this->coordinator->start('scenarioOne');

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals('http://someurl.dev/my-super-route/firstStepName', $response->getTargetUrl());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Process scenario with alias "scenarioOne" is not registered
     */
    public function shouldNotStartWhenScenarioIsNotRegistered()
    {
        $this->coordinator->start('scenarioOne');
    }

    /**
     * @test
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function shouldNotStartWhenProcessIsNotValid()
    {
        $router = $this->getRouter();

        $processBuilder = $this->getProcessBuilder($this->getProcess());

        $processContext = $this->getProcessContext();
        $processContext->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->coordinator = $this->createCoordinator($router, $processBuilder, $processContext);
        $this->coordinator->registerScenario('scenarioOne', $this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));
        $this->coordinator->start('scenarioOne');
    }

    /**
     * @test
     */
    public function shouldShowDisplayAction()
    {
        $this->coordinator->registerScenario('scenarioOne', $this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));

        $this->assertEquals('displayActionResponse', $this->coordinator->display('scenarioOne', 'someStepName'));
    }

    /**
     * @test
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function shouldNotShowDisplayActionWhenProcessIsNotValid()
    {
        $router = $this->getRouter();

        $processBuilder = $this->getProcessBuilder($this->getProcess());

        $processContext = $this->getProcessContext();
        $processContext->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->coordinator = $this->createCoordinator($router, $processBuilder, $processContext);
        $this->coordinator->registerScenario('scenarioOne', $this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));
        $this->coordinator->display('scenarioOne', 'someStepName');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Process scenario with alias "scenarioOne" is not registered
     */
    public function shouldNotShowDisplayActionWhenScenarioIsNotRegistered()
    {
        $this->coordinator->display('scenarioOne', 'someStepName');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Process scenario with alias "scenarioOne" is not registered
     */
    public function shouldNotShowForwardActionWhenScenarioIsNotRegistered()
    {
        $this->coordinator->forward('scenarioOne', 'someStepName');
    }

    /**
     * @test
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function shouldNotShowForwardWhenProcessIsNotValid()
    {
        $router = $this->getRouter();

        $processBuilder = $this->getProcessBuilder($this->getProcess());

        $processContext = $this->getProcessContext();
        $processContext->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->coordinator = $this->createCoordinator($router, $processBuilder, $processContext);
        $this->coordinator->registerScenario('scenarioOne', $this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));
        $this->coordinator->forward('scenarioOne', 'someStepName');
    }

    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\Process\Coordinator\Coordinator::forward
     */
    public function shouldShowForwardActionWhenStepIsNotCompleted()
    {
        $router = $this->getRouter();

        $processBuilder = $this->getProcessBuilder($this->getProcess());

        $processContext = $this->getProcessContext();
        $processContext->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true));
        $processContext->expects($this->any())
            ->method('isCompleted')
            ->will($this->returnValue(false));

        $this->coordinator = $this->createCoordinator($router, $processBuilder, $processContext);
        $this->coordinator->registerScenario('scenarioOne', $this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));

        $this->assertEquals('forwardActionResponse', $this->coordinator->forward('scenarioOne', 'someStepName'));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Process scenario with alias "scenarioOne" is already registered
     */
    public function shouldNotRegisterScenarioAgain()
    {
        $this->coordinator->registerScenario('scenarioOne', $this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));
        $this->coordinator->registerScenario('scenarioOne', $this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));
    }

    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\Process\Coordinator\Coordinator::forward
     */
    public function shouldRedirectToNextStepDisplayActionWhenStepIsCompleted()
    {
        $router = $this->getRouter(
            'sylius_flow_display',
             array(
                'scenarioAlias' => 'scenarioOne',
                'stepName' => 'nextStepName'
            ),
            'http://someurl.dev/step/scenarioOne/nextStepName'
        );

        $processBuilder = $this->getProcessBuilder($this->getProcess());

        $processContext = $this->getProcessContext();
        $processContext->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true));
        $processContext->expects($this->any())
            ->method('getNextStep')
            ->will($this->returnValue(
                $this->getStep('nextStepName')
            ));
        $processContext->expects($this->any())
            ->method('isCompleted')
            ->will($this->returnValue(true));

        $this->coordinator = $this->createCoordinator($router, $processBuilder, $processContext);
        $this->coordinator->registerScenario('scenarioOne', $this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));

        $response = $this->coordinator->forward('scenarioOne', 'someStepName');

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals('http://someurl.dev/step/scenarioOne/nextStepName', $response->getTargetUrl());
    }

    /**
     * @test
     * @covers Sylius\Bundle\FlowBundle\Process\Coordinator\Coordinator::forward
     */
    public function shouldDoProcessRedirectWhenLastStepIsCompleted()
    {

        $router = $this->getRouter(
            'http://localhost/processRedirect',
            array(),
            'http://localhost/processRedirect'
        );

        $processBuilder = $this->getProcessBuilder($this->getProcess());

        $processContext = $this->getProcessContext();
        $processContext->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true));
        $processContext->expects($this->any())
            ->method('isCompleted')
            ->will($this->returnValue(true));
        $processContext->expects($this->once())
            ->method('close');
        $processContext->expects($this->once())
            ->method('isLastStep')
            ->will($this->returnValue(true));

        $this->coordinator = $this->createCoordinator($router, $processBuilder, $processContext);
        $this->coordinator->registerScenario('scenarioOne', $this->getMock('Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface'));

        $response = $this->coordinator->forward('scenarioOne', 'someStepName');

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals('http://localhost/processRedirect', $response->getTargetUrl());
    }

    private function createCoordinator($router, $processBuilder, $processContext)
    {
        return new Coordinator(
            $router,
            $processBuilder,
            $processContext
        );
    }

    private function getRouter($route = '', $secondParam = array(), $url = 'http://someurl.dev')
    {
        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $router->expects($this->any())
            ->method('generate')
            ->with($this->equalTo($route), $this->equalTo($secondParam))
            ->will($this->returnValue($url));

        return $router;
    }

    private function getProcessBuilder($process)
    {
        $builder = $this->getMock('Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilderInterface');
        $builder->expects($this->any())
            ->method('build')
            ->will($this->returnValue(
                $process
            ));

        return $builder;
    }

    private function getProcessContext()
    {
        return $this->getMock('Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface');
    }

    private function getProcess()
    {
        $process = $this->getMock('Sylius\Bundle\FlowBundle\Process\ProcessInterface');
        $process->expects($this->any())
            ->method('getFirstStep')
            ->will($this->returnValue(
                $this->getStep('firstStepName')
            ));
        $process->expects($this->any())
            ->method('getStepByName')
            ->with($this->equalTo('someStepName'))
            ->will($this->returnValue(
                $this->getStep('someStepName')
            ));
        $process->expects($this->any())
            ->method('setScenarioAlias')
            ->with($this->equalTo('scenarioOne'));
        $process->expects($this->any())
            ->method('getScenarioAlias')
            ->will($this->returnValue('scenarioOne'));
        $process->expects($this->any())
            ->method('getRedirect')
            ->will($this->returnValue('http://localhost/processRedirect'));

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
