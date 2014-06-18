<?php

namespace Sylius\Bundle\FlowBundle\Tests\Validator;

use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\FlowBundle\Process\Process;
use Sylius\Bundle\FlowBundle\Process\Step\ControllerStep;
use Sylius\Bundle\FlowBundle\Validator\ProcessValidator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\PhpEngine;

class ProcessValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeValid()
    {
        $validator = new ProcessValidator('An error occurred.', null, function () {
            return true;
        });

        $this->assertTrue($validator->isValid());
    }

    /**
     * @test
     */
    public function shouldBeInvalid()
    {
        $validator = new ProcessValidator('An error occurred.', null, function () {
            return false;
        });

        $this->assertTrue(!$validator->isValid());
    }

    /**
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function shouldThrowException()
    {
        $process = new Process();

        $process->addStep('foo', new TestStep());

        $process->setValidator(new ProcessValidator('An error occurred.', null, function () {
            return false;
        }));

        if (!$process->getValidator()->isValid()) {
            $process->getValidator()->getResponse($process->getStepByName('foo'));
        }
    }
}

class TestStep extends ControllerStep
{
    public function displayAction(ProcessContextInterface $context)
    {
        // pufff.
    }
}

class Render extends PhpEngine
{
    public function renderResponse($view, array $parameters = array(), Response $response = null)
    {
        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($this->render($view, $parameters));

        return $response;
    }
}
