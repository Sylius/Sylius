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
use Sylius\Bundle\AdminBundle\Exception\CreateAdminUserFailedException;
use Sylius\Bundle\AdminBundle\Message\CreateAdminUser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Intl\Locales;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'sylius:admin-user:create',
    description: 'Create a new admin user',
)]
final class CreateAdminUserCommand extends Command
{
    use HandleTrait;

    protected SymfonyStyle $io;

    public function __construct(
        MessageBusInterface $messageBus,
        private string $defaultLocaleCode,
        private QuestionFactoryInterface $questionFactory,
    ) {
        $this->messageBus = $messageBus;

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

        $this->io->title('Admin user creation');

        $adminUserData = $this->askAdminUserData();

        $this->showSummary($adminUserData);

        if (!$this->adminCreationConfirmed()) {
            $this->io->error('Admin user creation has been aborted.');

            return Command::INVALID;
        }

        try {
            $this->handle(new CreateAdminUser(...array_values($adminUserData)));
        } catch (HandlerFailedException $exception) {
            $this->io->error(
                $exception
                ->getNestedExceptionOfClass(CreateAdminUserFailedException::class)[0]
                ->getMessage(),
            );

            return Command::FAILURE;
        }

        $this->io->success('Admin user has been successfully created.');

        return Command::SUCCESS;
    }

    /** @return array<array-key, mixed> */
    private function askAdminUserData(): array
    {
        $adminUserData = [];

        $adminUserData['email'] = $this->io->askQuestion($this->questionFactory->createEmail());
        $adminUserData['username'] = $this->io->askQuestion(
            $this->questionFactory->createWithNotNullValidator('Username'),
        );
        $adminUserData['first_name'] = $this->io->ask('First name');
        $adminUserData['last_name'] = $this->io->ask('Last name');
        $adminUserData['plain_password'] = $this->io->askQuestion(
            $this->questionFactory->createWithNotNullValidator('Password', true),
        );

        $localeCodes = Locales::getNames();
        $adminUserData['locale_code'] = $this->io->choice('Locale code', $localeCodes, $this->defaultLocaleCode);

        $adminUserData['enabled'] = $this->io->confirm('Do you want to enable this admin user?', true);

        return $adminUserData;
    }

    /** @param array<array-key, mixed> $adminUserData */
    private function showSummary(array $adminUserData): void
    {
        $this->io->writeln('The following admin user will be created:');
        $this->io->table(
            [
                'Email', 'Username', 'First name', 'Last name', 'Locale code', 'Enabled',
            ],
            [
                [
                    $adminUserData['email'],
                    $adminUserData['username'],
                    $adminUserData['first_name'],
                    $adminUserData['last_name'],
                    $adminUserData['locale_code'],
                    $adminUserData['enabled'] ? 'Yes' : 'No',
                ],
            ],
        );
    }

    private function adminCreationConfirmed(): bool
    {
        return $this->io->confirm('Do you want to save this admin user?');
    }
}
