<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\AdminUser;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'sylius:admin-user:create',
    description: 'Create a new admin user'
)]
class CreateAdminUserCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepositoryInterface $adminUserRepository,
    ) {
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->title('Admin user creation');

        $confirm = $this->io->confirm('Do you want to create an admin user ?', true);

        if (!$confirm) {
            return Command::INVALID;
        }

        do {
            $email = $this->io->ask('Email', null);
        }
        while (!$this->validateEmail($email));

        if ($this->checkIfAdminUserExists($email)) {
            $this->io->error(sprintf('Admin user with email address %s already exists.', $email));
            return COMMAND::FAILURE;
        }

        $userName = $this->io->ask('Username', null);
        $firstName = $this->io->ask('Firstname', null);
        $lastName = $this->io->ask('Lastname', null);
        $password = $this->io->askHidden('Password');

        $adminUser = new AdminUser();
        $adminUser->setEmail($email);
        $adminUser->setEncoderName('argon2i');
        $adminUser->setPlainPassword($password);
        $adminUser->setUsername($userName);
        $adminUser->setFirstName($firstName);
        $adminUser->setLastName($lastName);
        $adminUser->setLocaleCode('en_US');
        $adminUser->setEnabled(true);

        $this->entityManager->persist($adminUser);
        $this->entityManager->flush();

        $this->io->success(sprintf('Admin user %s was successfully created', $adminUser->getEmail()));

        return Command::SUCCESS;
    }

    private function validateEmail(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->io->error('Email address is not valid. Please, enter a valid address');
            return false;
        }

        return true;
    }

    private function checkIfAdminUserExists(string $email): bool
    {
        return null !== $this->adminUserRepository->findOneByEmail($email);
    }
}
