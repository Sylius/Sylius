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
    description: 'Deletes an admin user account by the given e-mail address',
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

        $email = $this->io->ask('Admin E-Mail-Address');
        $adminEmailAddress = $this->adminUserRepository->findOneByEmail($email);

        if ($adminEmailAddress === null)
        {
            $this->io->error(sprintf('Admin Account with the Email Address "%s" does not exist', $email));
            return Command::INVALID;
        }

        $this->adminUserRepository->remove($adminEmailAddress);

        $this->io->success(sprintf('Admin Account with the Email Address "%s" has been deleted successfully', $email));

        return Command::SUCCESS;
    }
}
