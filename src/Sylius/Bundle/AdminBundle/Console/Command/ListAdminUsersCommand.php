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

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'sylius:admin-user:list',
    description: 'Displays a list of all admin users along with their details.',
)]
final class ListAdminUsersCommand extends Command
{
    protected SymfonyStyle $io;

    /** @param UserRepositoryInterface<AdminUserInterface> $adminUserRepository */
    public function __construct(
        private readonly UserRepositoryInterface $adminUserRepository,
        private readonly EntityManagerInterface $adminManager,
    ) {
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function configure(): void
    {
        $this->addOption(
            'search',
            's',
            InputOption::VALUE_OPTIONAL,
            'Filter users based on a search term. If not provided, all users will be displayed.',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->title('List available admin users');

        $searchTerm = $input->getOption('search');
        if ($searchTerm === null || $searchTerm === '') {
            $this->renderUsersTable($output, $this->adminUserRepository->findAll());

            return Command::SUCCESS;
        }

        $users = $this->searchUsers($searchTerm);
        if (empty($users)) {
            $this->io->warning(sprintf('No users found matching "%s"', $searchTerm));

            return Command::SUCCESS;
        }

        $this->io->success(sprintf('Found %d user(s) matching "%s"', count($users), $searchTerm));
        $this->renderUsersTable($output, $users);

        return Command::SUCCESS;
    }

    /**
     * @return array<AdminUserInterface>
     */
    private function searchUsers(string $searchTerm): array
    {
        $criteria = '%' . mb_strtolower($searchTerm) . '%';

        return $this->adminManager->createQueryBuilder()
            ->from(AdminUserInterface::class, 'u')
            ->select('u')
            ->where('LOWER(u.email) LIKE :criteria
                OR LOWER(u.username) LIKE :criteria
                OR LOWER(u.firstName) LIKE :criteria
                OR LOWER(u.lastName) LIKE :criteria')
            ->setParameter('criteria', $criteria)
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array<AdminUserInterface> $adminUsers
     */
    private function renderUsersTable(OutputInterface $output, array $adminUsers): void
    {
        $table = new Table($output);
        $table->setHeaders([
            'ID', 'E-Mail', 'Username', 'First name', 'Last name', 'Locale code', 'Enabled', 'Roles',
        ]);

        foreach ($adminUsers as $adminUser) {
            $table->addRow([
                $adminUser->getId(),
                $adminUser->getEmail(),
                $adminUser->getUsername(),
                $adminUser->getFirstName() ?? '',
                $adminUser->getLastName() ?? '',
                $adminUser->getLocaleCode(),
                $adminUser->isEnabled() ? 'Enabled' : 'Disabled',
                $adminUser->getRoles() !== [] ? implode(', ', $adminUser->getRoles()) : 'No roles assigned',
            ]);
        }

        $tableStyle = new TableStyle();
        $tableStyle->setPadType(\STR_PAD_BOTH);
        $table->setStyle($tableStyle)->render();
    }
}
