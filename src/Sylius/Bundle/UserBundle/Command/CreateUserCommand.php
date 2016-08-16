<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class CreateUserCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:user:create')
            ->setDescription('Creates a new user account.')
            ->setDefinition([
                new InputArgument('type', InputArgument::REQUIRED, 'Type'),
                new InputArgument('email', InputArgument::REQUIRED, 'Email'),
                new InputArgument('username', InputArgument::REQUIRED, 'Username'),
                new InputArgument('password', InputArgument::REQUIRED, 'Password'),
                new InputArgument('roles', InputArgument::IS_ARRAY, 'Security roles'),
                new InputOption('disabled', null, InputOption::VALUE_NONE, 'Set the user as a disabled user'),
            ])
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates a new user account.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        $email = $input->getArgument('email');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $roles = $input->getArgument('roles');
        $disabled = $input->getOption('disabled');

        $securityRoles = ['ROLE_USER', 'ROLE_ADMINISTRATION_ACCESS'];
        foreach ($roles as $role) {
            $securityRoles[] = $role;
        }

        $user = $this->createUser(
            $type,
            $email,
            $username,
            $password,
            !$disabled,
            $securityRoles
        );

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        $output->writeln(sprintf('Created user <comment>%s</comment>', $email));
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $users = $this->getContainer()->getParameter('sylius.user.users');
        $configuredUsers = [];
        foreach ($users as $type => $user) {
            $configuredUsers[] = sprintf('%s_user', $type);
        }

        $output->writeln(sprintf('There are configured %s user types. [%s]', count($configuredUsers), implode(', ', $configuredUsers)));
        if (!$input->getArgument('type')) {
            $type = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose user type:',
                function ($type) use ($configuredUsers) {
                    if (!in_array($type, $configuredUsers, true)) {
                        throw new \Exception(sprintf('There is no configured %s. There are only %s', $type, implode(', ', $configuredUsers)));
                    }

                    return $type;
                }
            );

            $input->setArgument('type', $type);
        }

        if (!$input->getArgument('email')) {
            $email = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please enter an email:',
                function ($email) {
                    if (empty($email)) {
                        throw new \Exception('Email can not be empty');
                    }

                    return $email;
                }
            );

            $input->setArgument('email', $email);
        }

        if (!$input->getArgument('username')) {
            $username = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please enter an username:',
                function ($username) {
                    if (empty($username)) {
                        throw new \Exception('Username can not be empty');
                    }

                    return $username;
                }
            );

            $input->setArgument('username', $username);
        }

        if (!$input->getArgument('password')) {
            $password = $this->getHelper('dialog')->askHiddenResponseAndValidate(
                $output,
                'Please choose a password:',
                function ($password) {
                    if (empty($password)) {
                        throw new \Exception('Password can not be empty');
                    }

                    return $password;
                }
            );

            $input->setArgument('password', $password);
        }

        if (!$input->getArgument('roles')) {
            $roles = $this->getHelper('dialog')->ask(
                $output,
                'Please enter user\'s roles (separated by space):'
            );

            if (!empty($roles)) {
                $input->setArgument('roles', explode(' ', $roles));
            }
        }
    }

    /**
     * @param string $type
     * @param string $email
     * @param string $username
     * @param string $password
     * @param bool $enabled
     * @param array $securityRoles
     *
     * @return ShopUserInterface
     */
    protected function createUser($type, $email, $username, $password, $enabled, array $securityRoles)
    {
        /** @var UserInterface $user */
        $user = $this->getUserFactory($type)->createNew();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setRoles($securityRoles);
        $user->setEnabled($enabled);

        return $user;
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @param string $type
     *
     * @return FactoryInterface
     */
    protected function getUserFactory($type)
    {
        return $this->getContainer()->get(sprintf('sylius.factory.%s', $type));
    }
}
