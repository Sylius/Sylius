<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Cli;

use Behat\Behat\Context\Context;
use Sylius\Bundle\InstallerBundle\Command\InstallSampleDataCommand;
use Sylius\Bundle\InstallerBundle\Command\SetupCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
final class InstallerContext implements Context
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Application
     */
    private $application;

    /**
     * @var CommandTester
     */
    private $tester;

    /**
     * @var DialogHelper
     */
    private $dialog;

    /**
     * @var SetupCommand
     */
    private $command;

    /**
     * @var array
     */
    private $inputChoices = [
        'currency' => '',
        'name' => ' Name',
        'surname' => ' Surname',
        'e-mail' => ' test@email.com',
        'password' => ' pswd',
        'confirmation' => ' pswd',
    ];

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @When I run Sylius CLI installer
     */
    public function iRunSyliusCommandLineInstaller()
    {
        $this->application = new Application($this->kernel);
        $this->application->add(new SetupCommand());

        $this->command = $this->application->find('sylius:install:setup');
        $this->tester = new CommandTester($this->command);

        $this->iExecuteCommandWithInputChoices('sylius:install:setup');
    }

    /**
     * @Given I run Sylius Install Load Sample Data command
     */
    public function iRunSyliusInstallSampleDataCommand()
    {
        $this->application = new Application($this->kernel);
        $this->application->add(new InstallSampleDataCommand());
        $this->command = $this->application->find('sylius:install:sample-data');
        $this->tester = new CommandTester($this->command);
    }

    /**
     * @Given I confirm loading sample data
     */
    public function iConfirmLoadingData()
    {
        $this->iExecuteCommandAndConfirm('sylius:install:sample-data');
    }

    /**
     * @Then the command should finish successfully
     */
    public function commandSuccess()
    {
        expect($this->tester->getStatusCode())->toBe(0);
    }

    /**
     * @Then I should see output :text
     */
    public function iShouldSeeOutput($text)
    {
        \PHPUnit_Framework_Assert::assertContains($text, $this->tester->getDisplay());
    }

    /**
     * @Given I do not provide a currency
     */
    public function iDoNotProvideCurrency()
    {
        $this->inputChoices['currency'] = '';
    }

    /**
     * @Given I do not provide a name
     */
    public function iDoNotProvideName()
    {
        array_splice($this->inputChoices, 1, 0, '');
    }

    /**
     * @Given I do not provide a surname
     */
    public function iDoNotProvideSurname()
    {
        array_splice($this->inputChoices, 2, 0, '');
    }

    /**
     * @Given I do not provide an email
     */
    public function iDoNotProvideEmail()
    {
        array_splice($this->inputChoices, 3, 0, '');
    }

    /**
     * @Given I do not provide a correct email
     */
    public function iDoNotProvideCorrectEmail()
    {
        array_splice($this->inputChoices, 3, 0, 'email');
    }

    /**
     * @Given I provide currency :code
     */
    public function iProvideCurrency($code)
    {
        $this->inputChoices['currency'] = $code;
    }

    /**
     * @Given I provide full administrator data
     */
    public function iProvideFullAdministratorData()
    {
        $this->inputChoices['name'] = 'AdminName';
        $this->inputChoices['surname'] = 'AdminSurname';
        $this->inputChoices['e-mail'] = 'test@admin.com';
        $this->inputChoices['password'] = 'pswd1$';
        $this->inputChoices['confirmation'] = $this->inputChoices['password'];
    }

    /**
     * @param string $input
     *
     * @return resource
     */
    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }

    /**
     * @param string $name
     */
    private function iExecuteCommandWithInputChoices($name)
    {
        $this->dialog = $this->command->getHelper('dialog');
        $inputString = join(PHP_EOL, $this->inputChoices);
        $this->dialog->setInputStream($this->getInputStream($inputString.PHP_EOL));

        $this->tester->execute(['command' => $name]);
    }

    /**
     * @param string $name
     */
    private function iExecuteCommandAndConfirm($name)
    {
        $this->dialog = $this->command->getHelper('dialog');
        $inputString = 'y'.PHP_EOL;
        $this->dialog->setInputStream($this->getInputStream($inputString));

        $this->tester->execute(['command' => $name]);
    }
}
