<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UserBundle\Command;

use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PromoteUserCommand extends AbstractRoleCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('sylius:user:promote')
            ->setDescription('Promotes a user by adding roles.')
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'Email'),
                new InputArgument('roles', InputArgument::IS_ARRAY, 'Security roles'),
                new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Set the user as a super admin'),
                new InputOption('user-type', null, InputOption::VALUE_REQUIRED, 'User type'),
            ])
            ->setHelp(<<<EOT
The <info>sylius:user:promote</info> command promotes a user by adding security roles

  <info>php app/console sylius:user:promote matthieu@email.com</info>
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function executeRoleCommand(InputInterface $input, OutputInterface $output, UserInterface $user, array $securityRoles): void
    {
        $error = false;

        foreach ($securityRoles as $securityRole) {
            if ($user->hasRole($securityRole)) {
                $output->writeln(sprintf('<error>User "%s" did already have "%s" security role.</error>', $user->getEmail(), $securityRole));
                $error = true;

                continue;
            }

            $user->addRole($securityRole);
            $output->writeln(sprintf('Security role <comment>%s</comment> has been added to user <comment>%s</comment>', $securityRole, $user->getEmail()));
        }

        if (!$error) {
            $this->getEntityManager($input->getOption('user-type'))->flush();
        }
    }
}
