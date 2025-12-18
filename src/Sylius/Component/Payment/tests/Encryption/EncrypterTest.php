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

namespace Tests\Sylius\Component\Payment\Encryption;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Payment\Encryption\Encrypter;
use Sylius\Component\Payment\Encryption\EncrypterInterface;
use Sylius\Component\Payment\Encryption\Exception\EncryptionException;

final class EncrypterTest extends TestCase
{
    /** @var EncrypterInterface&MockObject */
    private MockObject $encrypterMock;

    private Encrypter $encrypter;

    protected function setUp(): void
    {
        $this->encrypterMock = $this->createMock(EncrypterInterface::class);
        $this->encrypter = new Encrypter('');
    }

    public function testImplementsEncrypter(): void
    {
        $this->assertInstanceOf(EncrypterInterface::class, $this->encrypter);
    }

    public function testThrowsAnExceptionIfItCannotEncrypt(): void
    {
        $this->expectException(EncryptionException::class);
        $this->encrypter->encrypt('data');
    }

    public function testThrowsAnExceptionIfItCannotDecrypt(): void
    {
        $this->expectException(EncryptionException::class);
        $this->encrypter->decrypt('data#ENCRYPTED');
    }

    public function testEncryptsData(): void
    {
        $this->encrypterMock
            ->expects($this->once())
            ->method('encrypt')
            ->with('data')
            ->willReturn('#ENCRYPTED');

        $result = $this->encrypterMock->encrypt('data');

        $this->assertIsString($result);
        $this->assertNotSame('data', $result);
        $this->assertStringEndsWith('#ENCRYPTED', $result);
    }

    public function testDecryptsData(): void
    {
        $encryptedData = 'data#ENCRYPTED';

        $this->encrypterMock
            ->expects($this->once())
            ->method('decrypt')
            ->with($encryptedData)
            ->willReturn('data');

        $result = $this->encrypterMock->decrypt($encryptedData);

        $this->assertSame('data', $result);
        $this->assertStringEndsNotWith('#ENCRYPTED', $result);
    }

    public function testDoesNothingWhenDataIsNotMarkedAsEncrypted(): void
    {
        $this->assertSame('data', $this->encrypter->decrypt('data'));
    }
}
