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

use Doctrine\Persistence\ObjectManager;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use SyliusLabs\Polyfill\Symfony\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Webmozart\Assert\Assert;

abstract class AbstractRoleCommand extends ContainerAwareCommand
{
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        // User types configured in the Bundle
        $availableUserTypes = $this->getAvailableUserTypes();
        if (empty($availableUserTypes)) {
            throw new \Exception(sprintf('At least one user type should implement %s', UserInterface::class));
        }

        $helper = $this->getHelper('question');
        Assert::isInstanceOf($helper, QuestionHelper::class);
        if (!$input->getOption('user-type')) {
            // Do not ask if there's only 1 user type configured
            if (count($availableUserTypes) === 1) {
                $input->setOption('user-type', $availableUserTypes[0]);
            } else {
                $question = new ChoiceQuestion('Please enter the user type:', $availableUserTypes, 1);
                $question->setErrorMessage('Choice %s is invalid.');
                $userType = $helper->ask($input, $output, $question);
                $input->setOption('user-type', $userType);
            }
        }

        $email = $input->getArgument('email');

        if ($email === null) {
            $question = new Question('Please enter an email:');
            $question->setValidator(function (?string $email) {
                if (!filter_var($email, \FILTER_VALIDATE_EMAIL)) {
                    throw new \RuntimeException('The email you entered is invalid.');
                }

                return $email;
            });
            $email = $helper->ask($input, $output, $question);
            $input->setArgument('email', $email);
        }

        $roles = $input->getArgument('roles');

        if ($roles === null) {
            $question = new Question('Please enter user\'s roles (separated by space):');
            $question->setValidator(function (?string $roles) {
                if ('' === $roles) {
                    throw new \RuntimeException('The value cannot be blank.');
                }

                return $roles;
            });
            $roles = $helper->ask($input, $output, $question);

            if (!empty($roles)) {
                $input->setArgument('roles', explode(' ', $roles));
            }
        }
    }

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

        return 0;
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function findUserByEmail(string $email, string $userType): UserInterface
    {
        /** @var UserInterface|null $user */
        $user = $this->getUserRepository($userType)->findOneByEmail($email);

        if (null === $user) {
            throw new \InvalidArgumentException(sprintf('Could not find user identified by email "%s"', $email));
        }

        return $user;
    }

    protected function getEntityManager(string $userType): ObjectManager
    {
        $class = $this->getUserModelClass($userType);

        return $this->getContainer()->get('doctrine')->getManagerForClass($class);
    }

    protected function getUserRepository(string $userType): UserRepositoryInterface
    {
        $class = $this->getUserModelClass($userType);

        $userRepository = $this->getEntityManager($userType)->getRepository($class);
        Assert::isInstanceOf($userRepository, UserRepositoryInterface::class);

        return $userRepository;
    }

    /** @return  array<string> */
    protected function getAvailableUserTypes(): array
    {
        $config = $this->getContainer()->getParameter('sylius.user.users');

        // Keep only users types which implement \Sylius\Component\User\Model\UserInterface
        $userTypes = array_filter($config, fn (array $userTypeConfig): bool => isset($userTypeConfig['user']['classes']['model']) && is_a($userTypeConfig['user']['classes']['model'], UserInterface::class, true));

        return array_keys($userTypes);
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function getUserModelClass(string $userType): string
    {
        $config = (array) $this->getContainer()->getParameter('sylius.user.users');
        if (empty($config[$userType]['user']['classes']['model'])) {
            throw new \InvalidArgumentException(sprintf('User type %s misconfigured.', $userType));
        }

        return $config[$userType]['user']['classes']['model'];
    }

    /** @param array<array-key, string> $securityRoles */
    abstract protected function executeRoleCommand(InputInterface $input, OutputInterface $output, UserInterface $user, array $securityRoles): void;
}

class_alias(AbstractRoleCommand::class, \Sylius\Bundle\UserBundle\Command\AbstractRoleCommand::class);
