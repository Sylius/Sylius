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
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
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
                new InputArgument('email', InputArgument::REQUIRED, 'Email'),
                new InputArgument('password', InputArgument::REQUIRED, 'Password'),
                new InputArgument('roles', InputArgument::IS_ARRAY, 'RBAC roles'),
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
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $roles = $input->getArgument('roles');
        $disabled = $input->getOption('disabled');

        $user = $this->createUser($email, $password, !$disabled, $roles);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        $output->writeln(sprintf('Created user <comment>%s</comment>', $email));
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('email')) {
            $email = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please enter an email:',
                function ($username) {
                    if (empty($username)) {
                        throw new \Exception('Email can not be empty');
                    }

                    return $username;
                }
            );

            $input->setArgument('email', $email);
        }

        if (null !== $this->getUserRepository()->findOneBy(array('username' => $input->getArgument('email')))) {
            throw new \InvalidArgumentException(sprintf('Username already taken "%s".', $input->getArgument('email')));
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
     * @param string $email
     * @param string $password
     * @param bool $enabled
     * @param array $roles
     *
     * @return UserInterface
     */
    protected function createUser($email, $password, $enabled, array $roles = array())
    {
        $canonicalizer = $this->getContainer()->get('sylius.user.canonicalizer');

        /*
         * @var UserInterface
         * @var $customer CustomerInterface
         */
        $user = $this->getUserFactory()->createNew();
        $customer = $this->getCustomerFactory()->createNew();
        $user->setCustomer($customer);
        $user->setUsername($email);
        $user->setEmail($email);
        $user->setUsernameCanonical($canonicalizer->canonicalize($user->getUsername()));
        $user->setEmailCanonical($canonicalizer->canonicalize($user->getEmail()));
        $user->setPlainPassword($password);
        $user->setEnabled($enabled);

        $this->getContainer()->get('sylius.user.password_updater')->updatePassword($user);

        if (!$this->isRbacEnabled()) {
            foreach ($roles as $role) {
                $user->addRole($role);
            }
            return $user;
        }

        foreach ($roles as $code) {
            /** @var \Sylius\Component\Rbac\Model\RoleInterface $role */
            $role = $this->getRoleRepository()->findOneBy(array('code' => $this->getRbacRoleCode($code)));

            if (null === $role) {
                throw new \InvalidArgumentException(
                    sprintf('RBAC role with code `%s` does not exist.', $code)
                );
            }

            $user->addAuthorizationRole($role);
        }

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
        return $this->getContainer()->get('sylius.factory.user');
    }

    /**
     * @return RepositoryInterface
     */
    protected function getUserRepository()
    {
        return $this->getContainer()->get('sylius.repository.user');
    }

    /**
     * @return FactoryInterface
     */
    protected function getCustomerFactory()
    {
        return $this->getContainer()->get('sylius.factory.customer');
    }

    /**
     * @return EntityRepository
     */
    protected function getRoleRepository()
    {
        return $this->getContainer()->get('sylius.repository.role');
    }

    /**
     * @param string $roleName
     *
     * @return string
     */
    protected function getRbacRoleCode($roleName)
    {
        /** @var \Sylius\Bundle\RbacBundle\Security\Role\InflectorInterface $inflector */
        $inflector = $this->getContainer()->get('sylius.rbac.role_inflector');

        return $inflector->toRbacRole($roleName);
    }

    /**
     * @return bool
     */
    protected function isRbacEnabled()
    {
        return array_key_exists('SyliusRbacBundle', $this->getContainer()->getParameter('kernel.bundles'));
    }
}
