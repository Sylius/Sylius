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
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

final class ChangeAdminPasswordContext implements Context
{
    private const ADMIN_USER_CHANGE_PASSWORD = 'sylius:admin-user:change-password';

    private Application $application;

    private ?CommandTester $commandTester = null;

    private $input = [];

    /** @param UserRepositoryInterface<AdminUserInterface> $adminUserRepository */
    public function __construct(
        KernelInterface $kernel,
        private UserRepositoryInterface $adminUserRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
        $this->application = new Application($kernel);
    }

    /**
     * @When I want to change password
     */
    public function iWantToChangePassword(): void
    {
        $command = $this->application->find(self::ADMIN_USER_CHANGE_PASSWORD);

        $this->commandTester = new CommandTester($command);
    }

    /**
     * @When I specify email as :email
     */
    public function iSpecifyEmailAs(string $email = ''): void
    {
        $this->input['email'] = $email;
    }

    /**
     * @When I specify my new password as :password
     */
    public function iSpecifyMyNewPassword(string $password = ''): void
    {
        $this->input['password'] = $password;
    }

    /**
     * @When I run command
     */
    public function iRunCommand(): void
    {
        $this->commandTester->setInputs($this->input);
        $this->commandTester->execute(['command' => self::ADMIN_USER_CHANGE_PASSWORD]);
    }

    /**
     * @Then I should be informed that password has been changed successfully
     */
    public function iShouldBeInformedThatPasswordHasBeenChangedSuccessfully(): void
    {
        Assert::contains($this->commandTester->getDisplay(), 'Admin user password has been changed successfully.');
    }

    /**
     * @Then I should be able to log in as :email authenticated by :password password
     */
    public function iShouldBeAbleToLoginWithEmailAndPassword(string $email = '', string $password = ''): void
    {
        /** @var AdminUserInterface|null $adminUser */
        $adminUser = $this->adminUserRepository->findOneByEmail($email);
        $adminUser->setPlainPassword($password);

        Assert::same($adminUser->getPassword(), $this->userPasswordHasher->hash($adminUser));
    }
}
