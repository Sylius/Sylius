<?php

namespace Sylius\Bundle\InstallerBundle\Command;

use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sylius:install')
            ->setDescription('Sylius installer.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Installing Sylius.</info>');
        $output->writeln('');

        $this
            ->checkStep($output)
            ->setupStep($input, $output)
        ;

        $output->writeln('<info>Sylius has been successfully installed.</info>');
    }

    protected function checkStep(OutputInterface $output)
    {
        $output->writeln('<info>Checking system requirements.</info>');

        $fulfilled = true;

        foreach ($this->getContainer()->get('sylius.requirements') as $collection) {
            $output->writeln(sprintf('<comment>%s</comment>', $collection->getLabel()));
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

    protected function setupStep(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Setting up database.</info>');

        $this->setupDatabase($input, $output);

        if ($this->getHelperSet()->get('dialog')->askConfirmation($output, '<question>Load fixtures (Y/N)?</question>', false)) {
            $this->setupFixtures($input, $output);
        }

        $output->writeln('');
        $output->writeln('<info>Administration setup.</info>');

        $this->setupAdmin($output);

        $output->writeln('');

        return $this;
    }

    protected function setupDatabase(InputInterface $input, OutputInterface $output)
    {
        $this
            ->runCommand('doctrine:database:create', $input, $output)
            ->runCommand('doctrine:schema:create', $input, $output)
            ->runCommand('doctrine:phpcr:repository:init', $input, $output)
            ->runCommand('assets:install', $input, $output)
            ->runCommand('assetic:dump', $input, $output)
        ;
    }

    protected function setupFixtures(InputInterface $input, OutputInterface $output)
    {
        $doctrineConfig = $this->getContainer()->get('doctrine.orm.entity_manager')->getConnection()->getConfiguration();
        $logger = $doctrineConfig->getSQLLogger();
        $doctrineConfig->setSQLLogger(null);
        $this
            ->runCommand('doctrine:fixtures:load', $input, $output)
            ->runCommand('doctrine:phpcr:fixtures:load', $input, $output)
        ;
        $doctrineConfig->setSQLLogger($logger);
    }

    protected function setupAdmin(OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');

        $userClass = $this->getContainer()->getParameter('sylius.model.user.class');
        $user = new $userClass;

        $user->setUsername($dialog->ask($output, '<question>Username:</question>'));
        $user->setPlainPassword($dialog->ask($output, '<question>Password:</question>'));
        $user->setEmail($dialog->ask($output, '<question>Email:</question>'));
        $user->setEnabled(true);
        $user->setRoles(array('ROLE_SYLIUS_ADMIN'));

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->persist($user);
        $em->flush();
    }

    protected function runCommand($command, InputInterface $input, OutputInterface $output)
    {
        $this
            ->getApplication()
            ->find($command)
            ->run($input, $output)
        ;

        return $this;
    }
}
