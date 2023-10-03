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

namespace Tests\Sylius\Bundle\CoreBundle\PriceHistory\Cli\Command;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\PriceHistory\Cli\Command\ClearPriceHistoryCommand;
use Sylius\Bundle\CoreBundle\PriceHistory\Remover\ChannelPricingLogEntriesRemoverInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class ClearPriceHistoryCommandTest extends TestCase
{
    private CommandTester $commandTester;

    private MockObject $remover;

    protected function setUp(): void
    {
        parent::setUp();

        $this->remover = $this->createMock(ChannelPricingLogEntriesRemoverInterface::class);

        $this->commandTester = new CommandTester(new ClearPriceHistoryCommand($this->remover));
    }

    /**
     * @test
     *
     * @dataProvider getInvalidDays
     */
    public function it_does_not_clear_pricing_history_when_number_of_days_is_invalid(mixed $days): void
    {
        $this->remover->expects($this->never())->method('remove');

        $this->commandTester->execute(['days' => $days]);

        $this->assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
        $this->assertStringContainsString(
            'Number of days must be an integer greater than 0',
            $this->commandTester->getDisplay(),
        );
    }

    /**
     * @test
     *
     * @dataProvider getValidDays
     */
    public function it_clears_pricing_history_when_non_interactive(int|string $days): void
    {
        $this->remover->expects($this->once())->method('remove');

        $this->commandTester->execute(['days' => $days], ['interactive' => false]);

        $this->commandTester->assertCommandIsSuccessful();
    }

    /** @test */
    public function it_asks_for_confirmation_when_interactive(): void
    {
        $this->remover->expects($this->once())->method('remove');

        $this->commandTester->setInputs(['yes']);
        $this->commandTester->execute(['days' => 30], ['interactive' => true]);

        $this->assertStringContainsString(
            'Are you sure you want to clear the price history from before 30 days ago?',
            $this->commandTester->getDisplay(),
        );
        $this->assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    /** @test */
    public function it_does_nothing_when_user_does_not_confirm(): void
    {
        $this->remover->expects($this->never())->method('remove');

        $this->commandTester->setInputs(['no']);
        $this->commandTester->execute(['days' => 30], ['interactive' => true]);

        $this->assertStringContainsString(
            'Are you sure you want to clear the price history from before 30 days ago?',
            $this->commandTester->getDisplay(),
        );
        $this->assertSame(Command::INVALID, $this->commandTester->getStatusCode());
    }

    public function getInvalidDays(): iterable
    {
        yield [0];
        yield ['0'];
        yield [0.0];
        yield ['0.0'];
        yield [-1];
        yield ['-1'];
        yield [-1.0];
        yield ['-1.0'];
        yield [1.1];
        yield ['1.1'];
        yield ['a'];
    }

    public function getValidDays(): iterable
    {
        yield [1];
        yield ['1'];
        yield [30];
        yield ['30'];
        yield [60];
        yield ['60'];
    }
}
