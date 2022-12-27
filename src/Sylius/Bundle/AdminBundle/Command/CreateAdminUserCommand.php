<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Command;

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Intl\Locales;

#[AsCommand(
    name: 'sylius:admin-user:create',
    description: 'Create a new admin user',
)]
final class CreateAdminUserCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private UserRepositoryInterface $adminUserRepository,
        private FactoryInterface $adminUserFactory,
        private CanonicalizerInterface $canonicalizer,
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

        $this->io->title('Admin user creation');

        $email = $this->io->askQuestion($this->createEmailQuestion());

        if ($this->adminUserExists($this->canonicalizer->canonicalize($email))) {
            $this->io->error(sprintf('Admin user with email address %s already exists.', $email));

            return Command::INVALID;
        }

        /** @var AdminUserInterface $adminUser */
        $adminUser = $this->adminUserFactory->createNew();

        $this->setAdminUserData($adminUser, $email);
        $this->showSummary($adminUser);

        if ($this->adminCreationConfirmed()) {
            $this->adminUserRepository->add($adminUser);

            $this->io->success('Admin user has been successfully created.');

            return Command::SUCCESS;
        }

        $this->io->error('Admin user creation has been aborted.');

        return Command::INVALID;
    }

    private function createEmailQuestion(): Question
    {
        $question = new Question('Email');
        $question->setValidator(function (?string $email) {
            if (!filter_var($email, \FILTER_VALIDATE_EMAIL) || $email === null) {
                throw new \InvalidArgumentException('The e-mail address provided is invalid. Please try again.');
            }

            return $email;
        });
        $question->setMaxAttempts(3);

        return $question;
    }

    private function adminUserExists(string $email): bool
    {
        return null !== $this->adminUserRepository->findOneByEmail($email);
    }

    private function setAdminUserData(AdminUserInterface $adminUser, string $email): void
    {
        $userName = $this->io->ask('Username');
        $firstName = $this->io->ask('First name');
        $lastName = $this->io->ask('Last name');
        $password = $this->io->askHidden('Password');

        $adminUser->setEmail($email);
        $adminUser->setPlainPassword($password);
        $adminUser->setUsername($userName);
        $adminUser->setFirstName($firstName);
        $adminUser->setLastName($lastName);

        $locales = Locales::getNames();

        $localeCode = $this->io->choice('Select the locale code', $locales, 'en_US');
        $adminUser->setLocaleCode($localeCode);

        $enabled = $this->io->confirm('Do you want to enable this admin user?', true);
        $adminUser->setEnabled($enabled);
    }

    private function showSummary(AdminUserInterface $adminUser): void
    {
        /**
         * @psalm-suppress UndefinedInterfaceMethod
         *
         * @phpstan-ignore-next-line
         */
        $username = $adminUser->getUsername();

        $this->io->writeln('The following admin user will be created:');
        $this->io->table(
            [
                'Email', 'Username', 'First name', 'Last name', 'Locale code', 'Enabled',
            ],
            [
                [
                    $adminUser->getEmail(),
                    $username,
                    $adminUser->getFirstName(),
                    $adminUser->getLastName(),
                    $adminUser->getLocaleCode(),
                    $adminUser->isEnabled() ? 'Yes' : 'No',
                ],
            ],
        );
    }

    private function adminCreationConfirmed(): bool
    {
        return $this->io->confirm('Do you want to save this admin user?');
    }
}
