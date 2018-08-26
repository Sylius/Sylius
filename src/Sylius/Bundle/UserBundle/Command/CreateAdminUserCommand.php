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
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Brett Bailey <bretto36@gmail.com>
 */
class CreateAdminUserCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:admin-user:create')
            ->setDescription('Creates a new admin user account.')
            ->setDefinition([
                new InputArgument('identifier', InputArgument::REQUIRED, 'Identifier'),
                new InputArgument('password', InputArgument::REQUIRED, 'Password'),
            ])
            ->setHelp(<<<EOT
The <info>%command.name%</info> command creates a new admin user account.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $identifier = $input->getArgument('identifier');
        $password = $input->getArgument('password');

        $user = $this->createUser(
            $identifier,
            $password
        );

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        $output->writeln(sprintf('Created user <comment>%s</comment>', $identifier));
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('identifier')) {
            $identifier = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please enter an identifier:',
                function ($username) {
                    if (empty($username)) {
                        throw new \Exception('Identifier can not be empty');
                    }

                    return $username;
                }
            );

            $input->setArgument('identifier', $identifier);
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
    }

    /**
     * @param string $identifier
     * @param string $password
     * @param array $securityRoles
     *
     * @return AdminUserInterface
     */
    protected function createUser($identifier, $password)
    {
        $canonicalizer = $this->getContainer()->get('sylius.user.canonicalizer');

        /** @var AdminUserInterface $user */
        $user = $this->getUserFactory()->createNew();
        $user->setUsername($identifier);
        $user->setEmail($identifier);
        $user->setUsernameCanonical($canonicalizer->canonicalize($user->getUsername()));
        $user->setEmailCanonical($canonicalizer->canonicalize($user->getEmail()));
        $user->setPlainPassword($password);
        $user->enable();
        $this->getContainer()->get('sylius.user.password_updater')->updatePassword($user);

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
     * @return FactoryInterface
     */
    protected function getUserFactory()
    {
        return $this->getContainer()->get('sylius.factory.admin_user');
    }
}
