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

use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

#[AsCommand(
    name: 'sylius:admin-user:delete',
    description: 'Deletes an admin user account by the given username',
)]
final class DeleteAdminUserCommand extends Command
{
    protected SymfonyStyle $io;

    /** @param UserRepositoryInterface<UserInterface> $adminUserRepository */
    public function __construct(
        private readonly UserRepositoryInterface $adminUserRepository
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

        $this->io->title('Delete admin user');

        $username = $this->io->ask('Admin username');
        $user = $this->adminUserRepository->findOneBy(['username' => $username]);

        if ($user === null) {
            $this->io->error(sprintf('Admin Account with the username "%s" does not exist', $username));
            return Command::INVALID;
        }

        $confirmationQuestion = $this->io->confirm(sprintf('Are you sure you want to delete the admin user "%s" ?', $username), false);
        if ($confirmationQuestion) {
            $this->adminUserRepository->remove($user);
            $this->io->success(sprintf('Admin Account with the username "%s" has been deleted successfully', $username));
        } else {
            return Command::INVALID;
        }

        return Command::SUCCESS;
    }
}
