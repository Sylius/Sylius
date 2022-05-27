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
use Behat\Gherkin\Node\TableNode;
use PhpSpec\Exception\Example\PendingException;
use Sylius\Bundle\CoreBundle\Command\SetupCommand;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository;
use Sylius\Bundle\OrderBundle\Command\ShowOrderCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

final class ShowOrderContext implements Context
{
    private KernelInterface $kernel;

    private ?Application $application = null;

    private ?CommandTester $tester = null;

    private ?Command $command = null;

    private OrderRepository $orderRepository;

    public function __construct(
        KernelInterface $kernel,
        OrderRepository $orderRepository,
    )
    {
        $this->kernel = $kernel;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @When I run show order command for order :arg1
     */
    public function runShowOrderCommand(string $number): void
    {
        $this->application = new Application($this->kernel);
        $this->application->add(new ShowOrderCommand());

        $this->command = $this->application->find('sylius:order:show');
        $this->tester = new CommandTester($this->command);

        $this->tester->execute(['command' => 'sylius:order:show']);
    }

    /**
     * @Then I should see the following information:
     */
    public function iShouldSeeTheFollowingInformation(TableNode $table): void
    {
        throw new PendingException();
    }
}
