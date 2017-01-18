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
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * @author Loïc Frémont <loic@mobizel.com>
 */
abstract class AbstractRoleCommand extends ContainerAwareCommand
{

    const ADMIN = 'admin';
    const SHOP = 'shop';

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('email')) {
            $question = new Question('Please enter an email:');
            $question->setValidator(function ($email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new \RuntimeException("The email you entered is invalid.");
                }
                return $email;
            });
            $email = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument('email', $email);
        }

        if (!$input->getArgument('roles')) {
            $question = new Question('Please enter user\'s roles (separated by space):');
            $question->setValidator(function ($roles) {
                if (strlen($roles) < 1) {
                    throw new \RuntimeException("The value cannot be blank.");
                }
                return $roles;
            });
            $roles = $this->getHelper('question')->ask($input, $output, $question);

            if (!empty($roles)) {
                $input->setArgument('roles', explode(' ', $roles));
            }
        }

        if (!$input->getOption('user-type')) {
            $question = new ChoiceQuestion('Please enter the user type: (admin/shop, defaults to shop)', [self::ADMIN, self::SHOP], 1);
            $question->setErrorMessage('Choice %s is invalid.');
            $repository = $this->getHelper('question')->ask($input, $output, $question);
            $input->setOption('user-type', $repository);
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
        $userType = $input->getOption('user-type');

        if ($superAdmin) {
            $securityRoles[] = 'ROLE_ADMINISTRATION_ACCESS';
        }

        /** @var UserInterface $user */
        $user = $this->findUserByEmail($email, $userType);

        $this->executeRoleCommand($input, $output, $user, $securityRoles);
    }

    /**
     * @param string $email
     * @param        $userType
     *
     * @return UserInterface
     * @throws \InvalidArgumentException
     */
    protected function findUserByEmail($email, $userType)
    {
        /** @var UserInterface $user */
        $user = $this->getUserRepository($userType)->findOneByEmail($email);

        if (null === $user) {
            throw new \InvalidArgumentException(sprintf('Could not find user identified by email "%s"', $email));
        }

        return $user;
    }

    /**
     * @param $userType
     * @return ObjectManager
     */
    protected function getEntityManager($userType)
    {
        return $this->getContainer()->get(sprintf('sylius.manager.%s_user', $userType));
    }

    /**
     * @param $userType
     * @return UserRepositoryInterface
     */
    protected function getUserRepository($userType)
    {
        return $this->getContainer()->get(sprintf('sylius.repository.%s_user', $userType));
    }

    /**
     * @param OutputInterface $output
     * @param UserInterface $user
     * @param array $securityRoles
     */
    abstract protected function executeRoleCommand(InputInterface $input, OutputInterface $output, UserInterface $user, array $securityRoles);
}
