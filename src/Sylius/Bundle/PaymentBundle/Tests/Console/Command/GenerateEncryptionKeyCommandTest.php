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

use Sylius\Bundle\PaymentBundle\Console\Command\GenerateEncryptionKeyCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

final class GenerateEncryptionKeyCommandTest extends KernelTestCase
{
    private const ENCRYPTION_KEY_PATH = __DIR__ . '/config/test.key';

    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $command = new GenerateEncryptionKeyCommand(new Filesystem(), self::ENCRYPTION_KEY_PATH);

        $this->commandTester = new CommandTester($command);
    }

    /** @test */
    public function it_generates_and_saves_the_encryption_key_in_path(): void
    {
        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());

        $this->assertStringContainsString('Generating encryption key for Sylius payment encryption', $output);
        $this->assertStringContainsString('Key has been generated and saved in', $output);
        $this->assertStringContainsString(self::ENCRYPTION_KEY_PATH, $this->normalizeString($output));
    }

    /** @test */
    public function it_does_not_overwrite_existing_key_when_it_is_not_requested(): void
    {
        $this->commandTester->setInputs(['Do you want to overwrite it?' => 'n']);
        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());

        $this->assertStringContainsString('Generating encryption key for Sylius payment encryption', $output);
        $this->assertStringContainsString('Do you want to overwrite it? (yes/no)', $output);
        $this->assertStringContainsString(
            $this->normalizeString(sprintf('"%s" already exists', self::ENCRYPTION_KEY_PATH)),
            $this->normalizeString($output),
        );
        $this->assertStringContainsString('[INFO] Key generation has been canceled', $output);
    }

    /** @test */
    public function it_overwrites_existing_key_when_requested(): void
    {
        $this->commandTester->setInputs(['Do you want to overwrite it?' => 'y']);
        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());

        $this->assertStringContainsString('Generating encryption key for Sylius payment encryption', $output);
        $this->assertStringContainsString('Do you want to overwrite it? (yes/no)', $output);
        $this->assertStringContainsString(
            $this->normalizeString(sprintf('"%s" already exists', self::ENCRYPTION_KEY_PATH)),
            $this->normalizeString($output),
        );
        $this->assertStringContainsString('Key has been generated and saved in', $output);
        $this->assertStringContainsString(self::ENCRYPTION_KEY_PATH, $this->normalizeString($output));
    }

    /** @test */
    public function it_automatically_overwrites_existing_key_when_overwrite_option_is_passed(): void
    {
        $this->commandTester->execute(['--overwrite' => true]);

        $output = $this->commandTester->getDisplay();

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());

        $this->assertStringContainsString('Generating encryption key for Sylius payment encryption', $output);
        $this->assertStringContainsString('Key has been generated and saved in', $output);
        $this->assertStringContainsString(self::ENCRYPTION_KEY_PATH, $this->normalizeString($output));
    }

    public static function tearDownAfterClass(): void
    {
        self::removeKey();
    }

    private static function removeKey(): void
    {
        if (file_exists(self::ENCRYPTION_KEY_PATH)) {
            unlink(self::ENCRYPTION_KEY_PATH);
            rmdir(dirname(self::ENCRYPTION_KEY_PATH));
        }
    }

    private function normalizeString(string $string): string
    {
        return preg_replace('/\s+/', '', $string);
    }
}
