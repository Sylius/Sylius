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

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Payment\Encryption\EncrypterInterface;
use Sylius\Component\Payment\Encryption\EntityEncrypterInterface;
use Sylius\Component\Payment\Encryption\Exception\EncryptionException;
use Sylius\Component\Payment\Encryption\GatewayConfigEncrypter;
use Sylius\Component\Payment\Model\GatewayConfigInterface;

#[AllowMockObjectsWithoutExpectations]
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

    public function testDoesNotDecryptConfigWhenFirstElementIsNotEncrypted(): void
    {
        $this->gatewayConfig
            ->expects($this->atLeastOnce())
            ->method('getConfig')
            ->willReturn([
                'key' => 'not_encrypted_value',
                'key-two' => 'encrypted_value#ENCRYPTED',
            ]);
        $this->encrypter
            ->expects($this->never())
            ->method('decrypt');
        $this->gatewayConfig
            ->expects($this->never())
            ->method('setConfig');

        $this->gatewayConfigEncrypter->decrypt($this->gatewayConfig);
    }

    public function testDecryptsWholeConfigWhenFirstElementIsEncryptedInNonStrictMode(): void
    {
        $this->gatewayConfig
            ->expects($this->atLeastOnce())
            ->method('getConfig')
            ->willReturn([
                'key' => 'encrypted_value#ENCRYPTED',
                'key-two' => 'not_encrypted_value',
            ]);
        $this->encrypter
            ->expects($this->exactly(2))
            ->method('decrypt')
            ->willReturnMap([
                ['encrypted_value#ENCRYPTED', serialize('value')],
                ['not_encrypted_value', serialize('other')],
            ]);
        $this->gatewayConfig
            ->expects($this->once())
            ->method('setConfig')
            ->with(['key' => 'value', 'key-two' => 'other']);

        $this->gatewayConfigEncrypter->decrypt($this->gatewayConfig);
    }

    public function testThrowsWhenDecryptingPartiallyEncryptedConfigInStrictMode(): void
    {
        $encrypter = $this->createMock(EncrypterInterface::class);
        $gatewayConfigEncrypter = new GatewayConfigEncrypter($encrypter, true, true);

        $gatewayConfig = $this->createMock(GatewayConfigInterface::class);
        $gatewayConfig
            ->expects($this->atLeastOnce())
            ->method('getConfig')
            ->willReturn([
                'key' => 'encrypted_value#ENCRYPTED',
                'key-two' => 'not_encrypted_value',
            ]);
        $encrypter
            ->expects($this->never())
            ->method('decrypt');
        $gatewayConfig
            ->expects($this->never())
            ->method('setConfig');

        $this->expectException(EncryptionException::class);

        $gatewayConfigEncrypter->decrypt($gatewayConfig);
    }

    public function testDecryptsFullyEncryptedConfigInStrictMode(): void
    {
        $encrypter = $this->createMock(EncrypterInterface::class);
        $gatewayConfigEncrypter = new GatewayConfigEncrypter($encrypter, true, true);

        $gatewayConfig = $this->createMock(GatewayConfigInterface::class);
        $gatewayConfig
            ->expects($this->atLeastOnce())
            ->method('getConfig')
            ->willReturn([
                'key' => 'encrypted_value#ENCRYPTED',
                'key-two' => 'encrypted_value-two#ENCRYPTED',
            ]);
        $encrypter
            ->expects($this->exactly(2))
            ->method('decrypt')
            ->willReturnMap([
                ['encrypted_value#ENCRYPTED', serialize('value')],
                ['encrypted_value-two#ENCRYPTED', serialize('TWO')],
            ]);
        $gatewayConfig
            ->expects($this->once())
            ->method('setConfig')
            ->with(['key' => 'value', 'key-two' => 'TWO']);

        $gatewayConfigEncrypter->decrypt($gatewayConfig);
    }

    public function testDecryptsWithExplicitAllowedClasses(): void
    {
        $encrypter = $this->createMock(EncrypterInterface::class);
        $gatewayConfigEncrypter = new GatewayConfigEncrypter($encrypter, [\stdClass::class]);

        $object = new \stdClass();
        $object->foo = 'bar';

        $gatewayConfig = $this->createMock(GatewayConfigInterface::class);
        $gatewayConfig
            ->expects($this->atLeastOnce())
            ->method('getConfig')
            ->willReturn(['key' => 'encrypted_value#ENCRYPTED']);
        $encrypter
            ->expects($this->once())
            ->method('decrypt')
            ->with('encrypted_value#ENCRYPTED')
            ->willReturn(serialize($object));
        $gatewayConfig
            ->expects($this->once())
            ->method('setConfig')
            ->with($this->callback(function (array $config): bool {
                return
                    isset($config['key']) &&
                    $config['key'] instanceof \stdClass &&
                    $config['key']->foo === 'bar'
                ;
            }));

        $gatewayConfigEncrypter->decrypt($gatewayConfig);
    }

    public function testDecryptsWithNoAllowedClasses(): void
    {
        $encrypter = $this->createMock(EncrypterInterface::class);
        $gatewayConfigEncrypter = new GatewayConfigEncrypter($encrypter, false);

        $gatewayConfig = $this->createMock(GatewayConfigInterface::class);
        $object = new \stdClass();
        $object->foo = 'bar';

        $gatewayConfig
            ->expects($this->atLeastOnce())
            ->method('getConfig')
            ->willReturn(['key' => 'encrypted_value#ENCRYPTED']);
        $encrypter
            ->expects($this->once())
            ->method('decrypt')
            ->with('encrypted_value#ENCRYPTED')
            ->willReturn(serialize($object));
        $gatewayConfig
            ->expects($this->once())
            ->method('setConfig')
            ->with($this->callback(function (array $config): bool {
                return isset($config['key']) && $config['key'] instanceof \__PHP_Incomplete_Class;
            }));

        $gatewayConfigEncrypter->decrypt($gatewayConfig);
    }
}
