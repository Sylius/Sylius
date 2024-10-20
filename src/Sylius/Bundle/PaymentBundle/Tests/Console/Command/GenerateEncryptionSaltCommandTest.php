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

namespace Sylius\Bundle\PaymentBundle\Tests\Console\Command;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PaymentBundle\Console\Command\GenerateEncryptionSaltCommand;
use Symfony\Component\Console\Tester\CommandTester;

final class GenerateEncryptionSaltCommandTest extends TestCase
{
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandTester = new CommandTester(new GenerateEncryptionSaltCommand());
    }

    /** @test */
    public function it_generates_encryption_salt(): void
    {
        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString('Generating encryption salt for Sylius payment encryption', $output);
        $this->assertStringContainsString('Salt:', $output);
        $this->assertStringContainsString('Please, remember to update your configuration with this salt', $output);
    }
}
