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

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'sylius:admin-user:delete',
    description: 'Deletes an admin user account by the given email',
)]
final class DeleteAdminUserCommand extends Command
{
    protected SymfonyStyle $io;

    /** @param UserRepositoryInterface<AdminUserInterface> $userRepository */
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Deletes an admin user')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the admin user to delete');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->title('Delete admin user');

        if ($input->isInteractive()) {
            $emailArgument = $input->getArgument('email');

            $email = $this->userRepository->findOneBy(['email' => $emailArgument]);
            if ($email === null) {
                $this->io->error(sprintf('Admin Account with the email "%s" does not exist', $emailArgument));

                return Command::FAILURE;
            }

            if (!$this->io->confirm(
                sprintf('Are you sure you want to delete the admin user "%s"?', $emailArgument),
                false,
            )) {
                return Command::FAILURE;
            }
        } else {
            $emailArgument = $input->getArgument('email');

            if (!$emailArgument) {
                $this->io->error('Email argument is required when using --no-interaction.');

                return Command::FAILURE;
            }

            $email = $this->userRepository->findOneBy(['email' => $emailArgument]);
            if ($email === null) {
                $this->io->error(sprintf('Admin Account with the email "%s" does not exist', $emailArgument));

                return Command::FAILURE;
            }
        }

        $this->userRepository->remove($email);
        $this->io->success(sprintf('Admin Account with the email "%s" has been deleted successfully', $emailArgument));

        return Command::SUCCESS;
    }
}
