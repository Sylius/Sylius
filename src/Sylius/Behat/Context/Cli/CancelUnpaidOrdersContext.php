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
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;

final class CancelUnpaidOrdersContext implements Context
{
    private const CANCEL_UNPAID_ORDERS_COMMAND = 'sylius:cancel-unpaid-orders';

    private Application $application;

    private ?CommandTester $commandTester = null;

    public function __construct(KernelInterface $kernel)
    {
        $this->application = new Application($kernel);
    }

    /**
     * @When the unpaid orders has been cancelled
     */
    public function runCancelUnpaidOrdersCommand(): void
    {
        $command = $this->application->find(self::CANCEL_UNPAID_ORDERS_COMMAND);

        $this->commandTester = new CommandTester($command);
        $this->commandTester->execute(['command' => self::CANCEL_UNPAID_ORDERS_COMMAND]);
    }

    /**
     * @Then I should see be informed that unpaid orders have been cancelled
     */
    public function shouldSeeOutputMessage(): void
    {
        Assert::contains($this->commandTester->getDisplay(), "Unpaid orders has been canceled");
    }
}
