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

namespace Sylius\Behat\Context\Cli;

use Behat\Behat\Context\Context;
use Behat\Step\Then;
use Behat\Step\When;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

final class CreateAdminUserContext implements Context
{
    private const CREATE_ADMIN_USER = 'sylius:admin-user:create';

    private const ADMINISTRATION_ACCESS = '0';

    private const API_ACCESS = '1';

    private const BOTH_ACCESS_LEVELS = '0,1';

    private Application $application;

    private ?CommandTester $commandTester = null;

    /** @var array<string, string> */
    private array $input = [];

    /** @param UserRepositoryInterface<AdminUserInterface> $adminUserRepository */
    public function __construct(
        KernelInterface $kernel,
        private readonly UserRepositoryInterface $adminUserRepository,
    ) {
        $this->application = new Application($kernel);
    }

    #[When('I want to create a new administrator using the command')]
    public function iWantToCreateANewAdministratorUsingTheCommand(): void
    {
        $command = $this->application->find(self::CREATE_ADMIN_USER);

        $this->commandTester = new CommandTester($command);
    }

    #[When('I specify its email as :email')]
    public function iSpecifyItsEmailAs(string $email = ''): void
    {
        $this->input['email'] = $email;
    }

    #[When('I specify its name as :username')]
    public function iSpecifyItsNameAs(string $username = ''): void
    {
        $this->input['username'] = $username;
    }

    #[When('I specify its first name as :firstName')]
    public function iSpecifyItsFirstNameAs(string $firstName = ''): void
    {
        $this->input['first_name'] = $firstName;
    }

    #[When('I specify its last name as :lastName')]
    public function iSpecifyItsLastNameAs(string $lastName = ''): void
    {
        $this->input['last_name'] = $lastName;
    }

    #[When('I specify its password as :password')]
    public function iSpecifyItsPasswordAs(string $password = ''): void
    {
        $this->input['password'] = $password;
    }

    #[When('I specify its locale as :localeCode')]
    public function iSpecifyItsLocaleAs(string $localeCode = ''): void
    {
        $this->input['locale_code'] = $localeCode;
    }

    #[When('I select administration access')]
    public function iSelectAdministrationAccess(): void
    {
        $this->input['access_levels'] = self::ADMINISTRATION_ACCESS;
    }

    #[When('I select API access')]
    public function iSelectApiAccess(): void
    {
        $this->input['access_levels'] = self::API_ACCESS;
    }

    #[When('I select both access levels')]
    public function iSelectBothAccessLevels(): void
    {
        $this->input['access_levels'] = self::BOTH_ACCESS_LEVELS;
    }

    #[When('I run the command')]
    public function iRunTheCommand(): void
    {
        $this->commandTester->setInputs([
            $this->input['email'] ?? '',
            $this->input['username'] ?? '',
            $this->input['first_name'] ?? '',
            $this->input['last_name'] ?? '',
            $this->input['password'] ?? '',
            $this->input['locale_code'] ?? '',
            'yes',
            $this->input['access_levels'] ?? self::ADMINISTRATION_ACCESS,
            'yes',
        ]);

        $this->commandTester->execute(['command' => self::CREATE_ADMIN_USER]);
    }

    #[Then('I should be informed that the admin user has been created')]
    public function iShouldBeInformedThatTheAdminUserHasBeenCreated(): void
    {
        Assert::contains($this->commandTester->getDisplay(), 'Admin user has been successfully created.');
    }

    #[Then('the :email administrator should have administration access')]
    public function theAdministratorShouldHaveAdministrationAccess(string $email): void
    {
        Assert::true($this->getAdminUser($email)->hasRole(AdminUserInterface::DEFAULT_ADMIN_ROLE));
    }

    #[Then('the :email administrator should not have administration access')]
    public function theAdministratorShouldNotHaveAdministrationAccess(string $email): void
    {
        Assert::false($this->getAdminUser($email)->hasRole(AdminUserInterface::DEFAULT_ADMIN_ROLE));
    }

    #[Then('the :email administrator should have API access')]
    public function theAdministratorShouldHaveApiAccess(string $email): void
    {
        Assert::true($this->getAdminUser($email)->hasRole(AdminUserInterface::API_ACCESS_ROLE));
    }

    #[Then('the :email administrator should not have API access')]
    public function theAdministratorShouldNotHaveApiAccess(string $email): void
    {
        Assert::false($this->getAdminUser($email)->hasRole(AdminUserInterface::API_ACCESS_ROLE));
    }

    private function getAdminUser(string $email): AdminUserInterface
    {
        /** @var AdminUserInterface|null $adminUser */
        $adminUser = $this->adminUserRepository->findOneByEmail($email);

        Assert::notNull($adminUser, sprintf('Administrator with email "%s" does not exist', $email));

        return $adminUser;
    }
}
