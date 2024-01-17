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

namespace Sylius\Bundle\AdminBundle\Console\Command;

use Sylius\Bundle\AdminBundle\Console\Command\Factory\QuestionFactoryInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'sylius:admin-user:change-password',
    description: 'Change password of admin user',
)]
final class ChangeAdminUserPasswordCommand extends Command
{
    protected SymfonyStyle $io;

    /** @param UserRepositoryInterface<AdminUserInterface> $adminUserRepository */
    public function __construct(
        private UserRepositoryInterface $adminUserRepository,
        private PasswordUpdaterInterface $passwordUpdater,
        private QuestionFactoryInterface $questionFactory,
    ) {
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->isInteractive()) {
            $this->io->error('This command must be run interactively.');

            return Command::FAILURE;
        }

        $this->io->title('Change admin user password');

        $email = $this->io->askQuestion($this->questionFactory->createEmail());

        /** @var AdminUserInterface|null $adminUser */
        $adminUser = $this->adminUserRepository->findOneByEmail($email);
        if ($adminUser === null) {
            $this->io->error(sprintf('Admin user with email address %s not found!', $email));

            return Command::INVALID;
        }

        $password = $this->io->askQuestion($this->questionFactory->createWithNotNullValidator('New password', true));
        $adminUser->setPlainPassword($password);

        $this->passwordUpdater->updatePassword($adminUser);
        $this->adminUserRepository->add($adminUser);

        $this->io->success('Admin user password has been changed successfully.');

        return Command::SUCCESS;
    }
}
