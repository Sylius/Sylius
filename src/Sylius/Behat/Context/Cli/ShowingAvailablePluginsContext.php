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
use Sylius\Bundle\CoreBundle\Command\SetupCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

final class ShowingAvailablePluginsContext implements Context
{
    /** @var Application */
    private $application;

    /** @var CommandTester */
    private $tester;

    /** @var SetupCommand */
    private $setupCommand;

    public function __construct(KernelInterface $kernel, SetupCommand $setupCommand)
    {
        $this->application = new Application($kernel);
        $this->application->add($setupCommand);

        $this->setupCommand = $this->application->find('sylius:show-available-plugins');
    }

    /**
     * @When I run show available plugins command
     */
    public function runShowAvailablePluginsCommand(): void
    {
        $this->tester = new CommandTester($this->setupCommand);
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
        Assert::contains($this->tester->getDisplay(), 'RBAC');
        Assert::contains($this->tester->getDisplay(), 'Refund');
    }
}
