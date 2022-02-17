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

use Webmozart\Assert\Assert;
use Behat\Behat\Context\Context;
use Sylius\Bundle\CoreBundle\Command\SetupCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;

final class CancelUnpaidOrdersContext implements Context
{
    private const COMMAND_CANCEL_UNPAID_ORDERS = 'sylius:cancel-unpaid-orders';

    private Application $application;

    private ?CommandTester $commandTester = null;

    public function __construct(KernelInterface $kernel)
    {
        $this->application = new Application($kernel);
        $this->application->add(new SetupCommand());
    }

    /**
     * @When I run command that cancels unpaid orders
     */
    public function runCancelUnpaidOrdersCommand(): void
    {
        $command = $this->application->find(self::COMMAND_CANCEL_UNPAID_ORDERS);

        $this->commandTester = new CommandTester($command);
        $this->commandTester->execute(['command' => self::COMMAND_CANCEL_UNPAID_ORDERS]);
    }

    /**
     * @Then I should see output :output message in terminal
     */
    public function shouldSeeOutputMessage(string $output): void
    {
        Assert::contains($this->commandTester->getDisplay(), $output);
    }
}
