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

use Sylius\Component\Core\Model\AdminUser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

#[AsCommand(
    name: 'sylius:admin-users:list',
    description: 'List all admin users',
)]
final class ListAdminUsersCommand extends Command
{
    protected SymfonyStyle $io;

    /** @param UserRepositoryInterface<AdminUserInterface> $userRepositoryInterface */
    public function __construct(
        private readonly UserRepositoryInterface $userRepositoryInterface
    ) {
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->title('List available admin users');

        $adminUsers = $this->userRepositoryInterface->findAll();
        /** @var AdminUser $adminUser */
        foreach ($adminUsers as $adminUser) {
            $this->io->table(
                [
                    'ID', 'E-Mail', 'Username', 'First name', 'Last name', 'Locale code', 'Enabled',
                ],
                [
                    [
                        $adminUser->getId(),
                        $adminUser->getEmail(),
                        $adminUser->getUsername(),
                        $adminUser->getFirstname() ?? 'No Firstname Set',
                        $adminUser->getLastName() ?? 'No Lastname Set',
                        $adminUser->getLocaleCode(),
                        $adminUser->isEnabled() ? 'Enabled' : 'Disabled',
                    ],
                ],
            );
        }
        return Command::SUCCESS;
    }
}
