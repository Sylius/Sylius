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
use Sylius\Component\Payment\Encryption\EncrypterInterface;
use Sylius\Component\Payment\Encryption\EntityEncrypterInterface;
use Sylius\Component\Payment\Encryption\GatewayConfigEncrypter;
use Sylius\Component\Payment\Model\GatewayConfigInterface;

final class GatewayConfigEncrypterTest extends TestCase
{
    private MockObject $encrypter;

    private GatewayConfigEncrypter $gatewayConfigEncrypter;

    /** @var GatewayConfigInterface&MockObject */
    private MockObject $gatewayConfig;

    protected function setUp(): void
    {
        $this->encrypter = $this->createMock(EncrypterInterface::class);
        $this->gatewayConfigEncrypter = new GatewayConfigEncrypter($this->encrypter);
        $this->gatewayConfig = $this->createMock(GatewayConfigInterface::class);
    }

    public function testAnEntityEncrypter(): void
    {
        $this->assertInstanceOf(EntityEncrypterInterface::class, $this->gatewayConfigEncrypter);
    }

    public function testDoesNothingWhenEncryptingEmptyGatewayConfig(): void
    {
        $this->gatewayConfig
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn([]);
        $this->encrypter
            ->expects($this->never())
            ->method('encrypt');
        $this->gatewayConfig
            ->expects($this->once())
            ->method('setConfig')
            ->with([]);

        $this->gatewayConfigEncrypter->encrypt($this->gatewayConfig);
    }

    public function testEncryptsScalarValuesInGatewayConfig(): void
    {
        $this->gatewayConfig
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn(['key' => 'value']);
        $this->encrypter
            ->expects($this->once())
            ->method('encrypt')
            ->with(serialize('value'))
            ->willReturn('encrypted_value');
        $this->gatewayConfig
            ->expects($this->once())
            ->method('setConfig')
            ->with(['key' => 'encrypted_value']);

        $this->gatewayConfigEncrypter->encrypt($this->gatewayConfig);
    }

    public function testEncryptsArrayValuesInGatewayConfig(): void
    {
        $this->gatewayConfig
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn(['key' => ['value', 'some_other_value']]);
        $this->encrypter
            ->expects($this->once())
            ->method('encrypt')
            ->with(serialize(['value', 'some_other_value']))
            ->willReturn('encrypted_value');
        $this->gatewayConfig
            ->expects($this->once())
            ->method('setConfig')
            ->with(['key' => 'encrypted_value']);

        $this->gatewayConfigEncrypter->encrypt($this->gatewayConfig);
    }

    public function testDoesNothingWhenDecryptingEmptyGatewayConfig(): void
    {
        $this->gatewayConfig
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn([]);
        $this->encrypter
            ->expects($this->never())
            ->method('decrypt');
        $this->gatewayConfig
            ->expects($this->never())
            ->method('setConfig');

        $this->gatewayConfigEncrypter->decrypt($this->gatewayConfig);
    }

    public function testDoesNotDecryptConfigWhenItsElementsAreNotEncryptedStrings(): void
    {
        $this->gatewayConfig
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn([
                'key' => 'not_encrypted_value',
                'key-two' => 'not_encrypted_value',
            ]);
        $this->encrypter
            ->expects($this->never())
            ->method('decrypt');
        $this->gatewayConfig
            ->expects($this->never())
            ->method('setConfig');

        $this->gatewayConfigEncrypter->decrypt($this->gatewayConfig);
    }

    public function testDecryptsScalarValuesInGatewayConfig(): void
    {
        $this->gatewayConfig
            ->expects($this->atLeastOnce())
            ->method('getConfig')
            ->willReturn(['key' => 'encrypted_value#ENCRYPTED']);
        $this->encrypter
            ->expects($this->once())
            ->method('decrypt')
            ->with('encrypted_value#ENCRYPTED')
            ->willReturn(serialize('value'));
        $this->gatewayConfig
            ->expects($this->once())
            ->method('setConfig')
            ->with(['key' => 'value']);

        $this->gatewayConfigEncrypter->decrypt($this->gatewayConfig);
    }

    public function testDecryptsArrayValuesInGatewayConfig(): void
    {
        $this->gatewayConfig
            ->expects($this->atLeastOnce())
            ->method('getConfig')
            ->willReturn([
                'key' => 'encrypted_value#ENCRYPTED',
                'key-two' => 'encrypted_value-two#ENCRYPTED',
            ]);
        $this->encrypter
            ->expects($this->exactly(2))
            ->method('decrypt')
            ->willReturnMap([
                ['encrypted_value#ENCRYPTED', serialize(['value', 'some_other_value'])],
                ['encrypted_value-two#ENCRYPTED', serialize('TWO')],
            ]);
        $this->gatewayConfig
            ->expects($this->once())
            ->method('setConfig')
            ->with(['key' => ['value', 'some_other_value'], 'key-two' => 'TWO']);

        $this->gatewayConfigEncrypter->decrypt($this->gatewayConfig);
    }
}
