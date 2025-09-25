<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Bundle\CoreBundle\Installer\Executor;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Installer\Executor\CommandExecutor;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

final class CommandExecutorTest extends TestCase
{
    private InputInterface&MockObject $input;

    private MockObject&OutputInterface $output;

    private Application&MockObject $application;

    private CommandExecutor $executor;

    protected function setUp(): void
    {
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
        $this->application = $this->createMock(Application::class);

        $this->executor = new CommandExecutor($this->input, $this->output, $this->application);
    }

    public function testShouldPreserveTheCurrentValueOfInteractiveOption(): void
    {
        $this->input->method('hasOption')->willReturnMap([
            ['no-interaction', true],
            ['env', true],
            ['verbose', true],
        ]);
        $this->input->method('getOption')->willReturnMap([
            ['no-interaction', false],
            ['env', 'dev'],
            ['verbose', true],
        ]);

        $this->application->expects($this->once())->method('setAutoExit')->with(false);

        $this->application
            ->expects($this->once())
            ->method('run')
            ->with(
                $this->callback(fn (ArrayInput $input) => $input->getFirstArgument() === 'command' &&
                    $input->getParameterOption('--no-debug') === true &&
                    $input->getParameterOption('--env') === 'dev' &&
                    $input->hasParameterOption('--verbose') &&
                    !$input->hasParameterOption('--no-interaction')),
                $this->isInstanceOf(NullOutput::class),
            )
            ->willReturn(0)
        ;

        $result = $this->executor->runCommand('command', []);
        $this->assertInstanceOf(CommandExecutor::class, $result);
    }

    public function testShouldUsePassedOptionsRatherThanDefaultParams(): void
    {
        $this->input->method('hasOption')->willReturnMap([
            ['no-interaction', true],
            ['env', true],
            ['verbose', true],
        ]);
        $this->input->method('getOption')->willReturnMap([
            ['no-interaction', false],
            ['env', 'dev'],
            ['verbose', true],
        ]);

        $this->application->expects($this->once())->method('setAutoExit')->with(false);

        $this->application
            ->expects($this->once())
            ->method('run')
            ->with(
                $this->callback(fn (ArrayInput $input) => $input->getFirstArgument() === 'command' &&
                    $input->getParameterOption('--no-debug') === true &&
                    $input->getParameterOption('--env') === 'dev' &&
                    $input->hasParameterOption('--verbose') &&
                    $input->hasParameterOption('--no-interaction')),
                $this->isInstanceOf(NullOutput::class),
            )
            ->willReturn(0)
        ;

        $result = $this->executor->runCommand('command', ['--no-interaction' => true]);
        $this->assertInstanceOf(CommandExecutor::class, $result);
    }
}
