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

namespace Sylius\Bundle\AdminBundle\Command;

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'sylius:admin-user:change-password',
    description: 'Change password of admin user'
)]
final class ChangeAdminUserPasswordCommand extends Command
{
    public function __construct(
        private UserRepositoryInterface $adminUserRepository,
        private PasswordUpdaterInterface $passwordUpdater
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if (!$input->isInteractive()) {
            $io->error('This command must be run interactively.');

            return Command::FAILURE;
        }

        $io->title('Change admin user password');

        $email = $io->askQuestion($this->createEmailQuestion());

        /** @var AdminUserInterface|null $adminUser */
        $adminUser = $this->adminUserRepository->findOneByEmail($email);
        if ($adminUser === null) {
            $io->error(sprintf('Admin user with email address %s not found!', $email));

            return Command::INVALID;
        }

        $password = $io->askHidden('Password');
        $adminUser->setPlainPassword($password);

        $this->passwordUpdater->updatePassword($adminUser);
        $this->adminUserRepository->add($adminUser);

        $io->success('Admin user password has been successfully reset.');

        return Command::SUCCESS;
    }

    private function createEmailQuestion(): Question
    {
        $question = new Question('Email');
        $question->setValidator(function (?string $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $email === null) {
                throw new \InvalidArgumentException('The e-mail address provided is invalid. Please try again.');
            }

            return $email;
        });
        $question->setMaxAttempts(3);

        return $question;
    }
}
