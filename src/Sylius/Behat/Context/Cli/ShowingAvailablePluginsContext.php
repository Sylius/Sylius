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
use Sylius\Bundle\CoreBundle\Command\SetupCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

final class ShowingAvailablePluginsContext implements Context
{
    private ?Application $application = null;

    private ?CommandTester $tester = null;

    private ?Command $command = null;

    public function __construct(private KernelInterface $kernel)
    {
    }

    /**
     * @When I run show available plugins command
     */
    public function runShowAvailablePluginsCommand(): void
    {
        $this->application = new Application($this->kernel);
        $this->application->add(new SetupCommand());

        $this->command = $this->application->find('sylius:show-available-plugins');
        $this->tester = new CommandTester($this->command);

        $this->tester->execute(['command' => 'sylius:show-available-plugins']);
    }

    /**
     * @Then I should see output :output with listed plugins
     */
    public function shouldSeeOutputWithListedPlugins(string $output): void
    {
        Assert::contains($this->tester->getDisplay(), $output);
        Assert::contains($this->tester->getDisplay(), 'Admin Order Creation');
        Assert::contains($this->tester->getDisplay(), 'Customer Order Cancellation');
        Assert::contains($this->tester->getDisplay(), 'Customer Reorder');
        Assert::contains($this->tester->getDisplay(), 'Invoicing');
        Assert::contains($this->tester->getDisplay(), 'Refund');
        Assert::contains($this->tester->getDisplay(), 'CMS');
    }
}
