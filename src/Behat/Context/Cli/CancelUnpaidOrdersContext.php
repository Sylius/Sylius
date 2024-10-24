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
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

final class CancelUnpaidOrdersContext implements Context
{
    private const CANCEL_UNPAID_ORDERS_COMMAND = 'sylius:cancel-unpaid-orders';

    private Application $application;

    private ?CommandTester $commandTester = null;

    public function __construct(
        KernelInterface $kernel,
        private OrderRepositoryInterface $orderRepository,
    ) {
        $this->application = new Application($kernel);
    }

    /**
     * @When I run cancel unpaid orders command
     */
    public function runCancelUnpaidOrdersCommand(): void
    {
        $command = $this->application->find(self::CANCEL_UNPAID_ORDERS_COMMAND);

        $this->commandTester = new CommandTester($command);
        $this->commandTester->execute(['command' => self::CANCEL_UNPAID_ORDERS_COMMAND]);
    }

    /**
     * @Then only the order with number :orderNumber should be canceled
     */
    public function onlyOrderWithNumberShouldBeCanceled(string $orderNumber): void
    {
        $orders = $this->orderRepository->findBy(['paymentState' => OrderPaymentStates::STATE_CANCELLED]);

        Assert::count($orders, 1);
        Assert::same($orders[0]->getNumber(), $orderNumber);
    }

    /**
     * @Then I should be informed that unpaid orders have been canceled
     */
    public function shouldBeInformedThatUnpaidOrdersHaveBeenCanceled(): void
    {
        Assert::contains($this->commandTester->getDisplay(), 'Unpaid orders have been canceled');
    }
}
