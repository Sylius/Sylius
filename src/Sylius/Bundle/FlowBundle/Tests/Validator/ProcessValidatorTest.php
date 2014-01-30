<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Tests\Validator;

use Sylius\Bundle\FlowBundle\Process\Process;
use Sylius\Bundle\FlowBundle\Tests\Fixtures\Render;
use Sylius\Bundle\FlowBundle\Tests\Fixtures\TestStep;
use Sylius\Bundle\FlowBundle\Validator\ProcessValidator;
use Symfony\Bundle\FrameworkBundle\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\TemplateReference;

class ProcessValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeValid()
    {
        $validator = new ProcessValidator(function () {
            return true;
        });

        $this->assertTrue($validator->isValid());
    }

    /**
     * @test
     */
    public function shouldBeInvalid()
    {
        $validator = new ProcessValidator(function () {
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

        $process->setValidator(new ProcessValidator(function () {
            return false;
        }));

        if (!$process->getValidator()->isValid()) {
            $process->getValidator()->getResponse($process->getStepByName('foo'));
        }
    }

    /**
     * @test
     */
    public function shouldGetTemplateResponse()
    {
        $message = "Error!";
        $parser = $this->getMock('Symfony\Component\Templating\TemplateNameParserInterface');
        $parser
            ->expects($this->once())
            ->method('parse')
            ->with('error.html.php')
            ->will($this->returnValue(new TemplateReference('', '', 'error', 'html', 'php')))
        ;
        $locator = $this->getMock('Symfony\Component\Config\FileLocatorInterface');
        $locator
            ->expects($this->once())
            ->method('locate')
            ->will($this->returnValue(__DIR__.'/../DependencyInjection/Fixtures/Resources/views/error.html.php'))
        ;

        $engine = new Render($parser, new FilesystemLoader($locator));

        $process = new Process();

        $step = new TestStep();

        $container = $this->getMock('Symfony\Component\DependencyInjection\Container');
        $container
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue($engine));

        $step->setContainer($container);
        $process->addStep('foo', $step);

        $process->setValidator(new ProcessValidator(function () {
            return false;
        }, $message, 'error.html.php'));

        if (!$process->getValidator()->isValid()) {
            $response = $process->getValidator()->getResponse($process->getStepByName('foo'));
        }

        $this->assertSame($response->getContent(), $message);
    }
}
