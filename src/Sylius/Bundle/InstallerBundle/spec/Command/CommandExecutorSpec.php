<?php

namespace spec\Sylius\Bundle\InstallerBundle\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application;

class CommandExecutorSpec extends ObjectBehavior
{

    function let(InputInterface $input, OutputInterface $output, Application $application)
    {
        $this->beConstructedWith($input, $output, $application);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InstallerBundle\Command\CommandExecutor');
    }

    function it_should_preserve_the_current_value_of_interactive_option(InputInterface $input, Application $application)
    {
        $input->hasOption('no-interaction')
            ->willReturn(true);
        $input->getOption('no-interaction')
            ->willReturn(false);
        $input->hasOption('env')
            ->willReturn(true);
        $input->getOption('env')
            ->willReturn('dev');
        $input->hasOption('verbose')
            ->willReturn(true);
        $input->getOption('verbose')
            ->willReturn(true);
        $arrayInput = new ArrayInput(
            array(
                'command' => 'command',
                '--no-debug' => true,
                '--env' => 'dev',
                '--verbose' => true,
            )
        );
        $application->setAutoExit(false)->shouldBeCalled();
        $application->run($arrayInput, new NullOutput())->willReturn(0);

        $this->runCommand('command', array());
    }

    function it_should_use_passed_options_rather_than_default_params(InputInterface $input, Application $application)
    {
        $input->hasOption('no-interaction')
            ->willReturn(true);
        $input->getOption('no-interaction')
            ->willReturn(false);
        $input->hasOption('env')
            ->willReturn(true);
        $input->getOption('env')
            ->willReturn('dev');
        $input->hasOption('verbose')
            ->willReturn(true);
        $input->getOption('verbose')
            ->willReturn(true);
        $arrayInput = new ArrayInput(
            array(
                'command' => 'command',
                '--no-debug' => true,
                '--env' => 'dev',
                '--no-interaction' => true,
                '--verbose' => true,
            )
        );
        $application->setAutoExit(false)->shouldBeCalled();
        $application->run($arrayInput, new NullOutput())->willReturn(0);

        $this->runCommand('command', array('--no-interaction' => true));
    }
}
