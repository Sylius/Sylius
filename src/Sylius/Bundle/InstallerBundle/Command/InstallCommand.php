<?php

namespace Sylius\Bundle\InstallerBundle\Command;

use RuntimeException;
use Sylius\Bundle\InstallerBundle\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sylius:install')
            ->setDescription('Sylius installer.')
            ->addOption(
                'with-fixtures',
                null,
                InputOption::VALUE_NONE,
                'Install fixtures without asking'
            )
            ->addOption('admin-name', 'an', InputOption::VALUE_OPTIONAL, 'Administrator name')
            ->addOption('admin-password', 'ap', InputOption::VALUE_OPTIONAL, 'Administrator password')
            ->addOption('admin-email', 'ae', InputOption::VALUE_OPTIONAL, 'Administrator email');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Installing Sylius.</info>');
        $output->writeln('');

        $this
            ->checkRequiredArguments($input)
            ->checkStep($input, $output)
            ->setupStep($input, $output);

        $output->writeln('<info>Sylius has been successfully installed.</info>');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return $this
     * @throws \RuntimeException
     */
    protected function checkStep(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Checking system requirements.</info>');

        $fulfilled = true;

        foreach ($this->getContainer()->get('sylius.requirements') as $collection) {
            $output->writeln(sprintf('<comment>%s</comment>', $collection->getLabel()));
            /** @var $requirement Requirement */
            foreach ($collection as $requirement) {
                $output->write($requirement->getLabel());
                if ($requirement->isFulfilled()) {
                    $output->writeln(' <info>OK</info>');
                } else {
                    if ($requirement->isRequired()) {
                        $fulfilled = false;
                        $output->writeln(' <error>ERROR</error>');
                        $output->writeln(sprintf('<comment>%s</comment>', $requirement->getHelp()));
                    } else {
                        $output->writeln(' <comment>WARNING</comment>');
                    }
                }
            }
        }

        if (!$fulfilled) {
            throw new RuntimeException('Some system requirements are not fulfilled. Please check output messages and fix them.');
        }

        $output->writeln('');

        return $this;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return $this
     */
    protected function setupStep(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Setting up database.</info>');

        $this
            ->runCommand('doctrine:database:create', new ArrayInput(array('--no-interaction')), $output)
            ->runCommand('doctrine:schema:create', new ArrayInput(array('--no-interaction')), $output)
            ->runCommand('assetic:dump', new ArrayInput(array('--no-interaction')), $output);

        $output->writeln('');

        $this->installFixtures($input, $output);

        $this->createAdminUser($input, $output);

        $output->writeln('');

        return $this;
    }

    /**
     * @param string          $command
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return $this
     * @throws \RuntimeException
     */
    private function runCommand($command, InputInterface $input, OutputInterface $output)
    {
        $input->setInteractive(false);
        $returnCode = $this
            ->getApplication()
            ->find($command)
            ->run($input, $output);

        if ($returnCode !== 0) {
            throw new RuntimeException(sprintf(
                "Command '%s %s' failed during setup. See output for details.",
                $command,
                $input
            ));
        }

        return $this;
    }

    /**
     * Handles logic of installing fixtures
     *
     * ARGUMENTS                            | INSTALL FIXTURES?
     * none                                 | ask user
     * --with-fixtures                      | dont ask, install
     * --no-interaction --with-fixtures     | dont ask, install
     * --no-interaction                     | dont ask, dont install
     *
     * @param $input  InputInterface
     * @param $output OutputInterface
     *
     * @throws RuntimeException
     */
    protected function installFixtures(InputInterface $input, OutputInterface $output)
    {

        if (!$input->isInteractive() && !$input->getOption('with-fixtures')) {
            return;
        }

        if ($input->isInteractive() && !$input->getOption('with-fixtures')) {
            /** @var $dialog DialogHelper */
            $dialog = $this->getHelperSet()->get('dialog');
            if (!$dialog->askConfirmation($output, '<question>Load fixtures (Y/N)?</question>', false)) {
                return;
            }
        }

        $output->writeln('<info>Loading sample data.</info>');
        $this->runCommand(
            'doctrine:fixtures:load',
            new ArrayInput(array('--no-interaction')),
            $output
        );

        $output->writeln('');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function createAdminUser(InputInterface $input, OutputInterface $output)
    {
        $userClass = $this->getContainer()->getParameter('sylius.model.user.class');
        $user      = new $userClass;

        $output->writeln('<info>Creating administrator account.</info>');

        if ($input->isInteractive()) {
            /** @var $dialog DialogHelper */
            $dialog   = $this->getHelperSet()->get('dialog');
            $username = $dialog->ask($output, '<question>Username:</question>');
            $password = $dialog->ask($output, '<question>Password:</question>');
            $email    = $dialog->ask($output, '<question>Email:</question>');
        } else {
            $username = $input->getOption('admin-name');
            $password = $input->getOption('admin-password');
            $email    = $input->getOption('admin-email');

            $output->writeln("<info>Username:</info> " . $username);
            $output->writeln("<info>Password:</info> " . $password);
            $output->writeln("<info>Email:</info>    " . $email);
        }

        $user->setUsername($username);
        $user->setPlainPassword($password);
        $user->setEmail($email);
        $user->setEnabled(true);
        $user->setRoles(array('ROLE_SYLIUS_ADMIN'));

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->persist($user);
        $em->flush();
    }

    /**
     * @param InputInterface $input
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    protected function checkRequiredArguments(InputInterface $input)
    {
        if ($input->getOption('admin-name') === null) {
            throw new \InvalidArgumentException(
                'Option --admin-name must be specified when running non-interactive mode'
            );
        }
        if ($input->hasOption('admin-password') === null) {
            throw new \InvalidArgumentException(
                'Option --admin-password must be specified when running non-interactive mode'
            );
        }
        if ($input->hasOption('admin-email') === null) {
            throw new \InvalidArgumentException(
                'Option --admin-email must be specified when running non-interactive mode'
            );
        }

        return $this;
    }
}
