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

namespace Sylius\Behat\Context\Cli;

use Behat\Behat\Context\Context;
use Sylius\Bundle\CoreBundle\Command\InstallSampleDataCommand;
use Sylius\Bundle\CoreBundle\Command\SetupCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

final class InstallerContext implements Context
{
    /** @var KernelInterface */
    private $kernel;

    /** @var Application */
    private $application;

    /** @var CommandTester */
    private $tester;

    /** @var SetupCommand */
    private $setupCommand;

    /** @var InstallSampleDataCommand */
    private $installSampleDataCommand;

    /** @var array */
    private $inputChoices = [
        'currency' => 'USD',
        'e-mail' => 'test@email.com',
        'username' => 'test',
        'password' => 'pswd',
        'confirmation' => 'pswd',
    ];

    /**
     * @param KernelInterface $kernel
     * @param SetupCommand    $setupCommand
     * @param InstallSampleDataCommand    $setupCommand
     */
    public function __construct(
        KernelInterface $kernel,
        SetupCommand $setupCommand,
        InstallSampleDataCommand $installSampleDataCommand
    ) {
        $this->kernel = $kernel;
        $this->setupCommand = $setupCommand;
        $this->installSampleDataCommand = $installSampleDataCommand;
    }

    /**
     * @When I run Sylius CLI installer
     */
    public function iRunSyliusCommandLineInstaller(): void
    {
        $this->application = new Application($this->kernel);
        $this->application->add($this->setupCommand);

        $this->setupCommand = $this->application->find('sylius:install:setup');
        $this->tester = new CommandTester($this->setupCommand);

        $this->iExecuteCommandWithInputChoices('sylius:install:setup');
    }

    /**
     * @Given I run Sylius Install Load Sample Data command
     */
    public function iRunSyliusInstallSampleDataCommand(): void
    {
        $this->application = new Application($this->kernel);
        $this->application->add($this->installSampleDataCommand);
        $this->setupCommand = $this->application->find('sylius:install:sample-data');
        $this->tester = new CommandTester($this->setupCommand);
    }

    /**
     * @Given I confirm loading sample data
     */
    public function iConfirmLoadingData(): void
    {
        $this->iExecuteCommandAndConfirm('sylius:install:sample-data');
    }

    /**
     * @Then the command should finish successfully
     */
    public function commandSuccess(): void
    {
        Assert::same($this->tester->getStatusCode(), 0);
    }

    /**
     * @Then I should see output :text
     */
    public function iShouldSeeOutput(string $text): void
    {
        Assert::contains($this->tester->getDisplay(), $text);
    }

    /**
     * @Given I do not provide an email
     */
    public function iDoNotProvideEmail(): void
    {
        $this->inputChoices['e-mail'] = '';
    }

    /**
     * @Given I do not provide a correct email
     */
    public function iDoNotProvideCorrectEmail(): void
    {
        $this->inputChoices['e-mail'] = 'janusz';
    }

    /**
     * @Given I provide full administrator data
     */
    public function iProvideFullAdministratorData(): void
    {
        $this->inputChoices['e-mail'] = 'test@admin.com';
        $this->inputChoices['username'] = 'test';
        $this->inputChoices['password'] = 'pswd1$';
        $this->inputChoices['confirmation'] = $this->inputChoices['password'];
    }

    private function iExecuteCommandWithInputChoices(string $name): void
    {
        try {
            $this->tester->setInputs($this->inputChoices);
            $this->tester->execute(['command' => $name]);
        } catch (\Exception $e) {
        }
    }

    private function iExecuteCommandAndConfirm(string $name): void
    {
        try {
            $this->tester->setInputs(['y']);
            $this->tester->execute(['command' => $name]);
        } catch (\Exception $e) {
        }
    }
}
