<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Command;

use Sylius\Component\Core\Model\UserInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Loïc Frémont <loic@mobizel.com>
 */
class RbacPromoteUserCommand extends AbstractRbacRoleCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:user:rbac-promote')
            ->setDescription('Promotes a user by adding rbac roles.')
            ->setDefinition(array(
                new InputArgument('email', InputArgument::REQUIRED, 'Email'),
                new InputArgument('roles', InputArgument::IS_ARRAY, 'RBAC roles'),
                new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Set the user as a super admin'),
            ))
            ->setHelp(<<<EOT
The <info>%command.name%</info> command promotes a user by adding RBAC roles

  <info>php app/console %command.name% matthieu@email.com</info>
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function executeRoleCommand(OutputInterface $output, UserInterface $user, array $roles)
    {
        $error = false;

        foreach ($roles as $code) {
            $role = $this->findAuthorizationRole($code);

            if ($user->hasAuthorizationRole($role)) {
                $output->writeln(sprintf('<error>User "%s" did already have "%s" RBAC role.</error>', (string)$user, $role));
                $error = true;
                continue;
            }

            $user->addAuthorizationRole($role);
            $output->writeln(sprintf('RBAC role <comment>%s</comment> has been added to user <comment>%s</comment>', $role, (string)$user));
        }

        if (!$error) {
            $this->getEntityManager()->flush();
        }
    }
}
