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

namespace Sylius\Bundle\UserBundle\Console\Command;

use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DemoteUserCommand extends AbstractRoleCommand
{
    protected static $defaultName = 'sylius:user:demote';

    protected function configure(): void
    {
        $this
            ->setDescription('Demotes a user by removing a role.')
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'Email'),
                new InputArgument('roles', InputArgument::IS_ARRAY, 'Security roles'),
                new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Unset the user as super admin'),
                new InputOption('user-type', null, InputOption::VALUE_REQUIRED, 'Use shop or admin user type'),
            ])
            ->setHelp(
                <<<EOT
The <info>sylius:user:demote</info> command demotes a user by removing security roles

  <info>php app/console sylius:user:demote matthieu@email.com</info>
EOT
            )
        ;
    }

    protected function executeRoleCommand(InputInterface $input, OutputInterface $output, UserInterface $user, array $securityRoles): void
    {
        $error = false;
        $successMessages = [];

        foreach ($securityRoles as $securityRole) {
            if (!$user->hasRole($securityRole)) {
                $output->writeln(sprintf('<error>User "%s" doesn\'t have "%s" Security role.</error>', $user->getEmail(), $securityRole));
                $error = true;

                continue;
            }

            $user->removeRole($securityRole);
            $successMessages[] = sprintf('Security role <comment>%s</comment> has been removed from user <comment>%s</comment>', $securityRole, $user->getEmail());
        }

        if (!$error) {
            $output->writeln($successMessages);
            $this->getEntityManager($input->getOption('user-type'))->flush();
        } else {
            $output->writeln(sprintf('<error>No roles removed from User "%s".</error>', $user->getEmail()));
        }
    }
}

class_alias(DemoteUserCommand::class, \Sylius\Bundle\UserBundle\Command\DemoteUserCommand::class);
