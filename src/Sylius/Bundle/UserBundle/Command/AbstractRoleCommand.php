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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Loïc Frémont <loic@mobizel.com>
 */
abstract class AbstractRoleCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('email')) {
            $email = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please enter an email:',
                function($email) {
                    if (empty($email)) {
                        throw new \Exception('Email can not be empty');
                    }

                    return $email;
                }
            );

            $input->setArgument('email', $email);
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
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $securityRoles = $input->getArgument('roles');
        $superAdmin = $input->getOption('super-admin');

        if ($superAdmin) {
            $securityRoles[] = 'ROLE_ADMINISTRATION_ACCESS';
        }

        /** @var UserInterface $user */
        $user = $this->findUserByEmail($email);

        $this->executeRoleCommand($output, $user, $securityRoles);
    }

    /**
     * @param string $email
     *
     * @return UserInterface
     */
    protected function findUserByEmail($email)
    {
        /** @var UserInterface $user */
        $user = $this->getUserRepository()->findOneByEmail($email);

        if (null === $user) {
            throw new \InvalidArgumentException(sprintf('Could not find user identified by email "%s"', $email));
        }

        return $user;
    }

    /**
     * @return ObjectManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('sylius.manager.user');
    }

    /**
     * @return UserRepositoryInterface
     */
    protected function getUserRepository()
    {
        return $this->getContainer()->get('sylius.repository.user');
    }

    /**
     * @param OutputInterface $output
     * @param UserInterface $user
     * @param array $securityRoles
     */
    abstract protected function executeRoleCommand(OutputInterface $output, UserInterface $user, array $securityRoles);
}
