<?php

namespace Sylius\Bundle\CoreBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Install command.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class InstallCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:install')
            ->setDescription('Install Sylius.')
            ->setDefinition(array(
                new InputOption('fixtures', null, InputOption::VALUE_NONE, 'Skip fixtures loading.'),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fixtures = $input->getOption('fixtures');

        $command = $this->getApplication()->find('doctrine:database:create');
        $arguments = array('command' => 'doctrine:database:create');

        $input = new ArrayInput($arguments);

        try {
            $command->run($input, $output);
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }

        $command = $this->getApplication()->find('doctrine:schema:create');
        $arguments = array('command' => 'doctrine:schema:create');

        $input = new ArrayInput($arguments);

        try {
            $command->run($input, $output);
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }

        $command = $this->getApplication()->find('assetic:dump');
        $arguments = array('command' => 'assetic:dump');

        $input = new ArrayInput($arguments);

        try {
            $command->run($input, $output);
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }

        if ($fixtures) {
            $command = $this->getApplication()->find('doctrine:fixtures:load');
            $arguments = array('command' => 'doctrine:fixtures:load');

            $input = new ArrayInput($arguments);

            try {
                $command->run($input, $output);
            } catch (\Exception $e) {
                $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            }
        }
    }
}
