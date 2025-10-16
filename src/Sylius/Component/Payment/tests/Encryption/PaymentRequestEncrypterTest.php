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
use Sylius\Component\Payment\Encryption\PaymentRequestEncrypter;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class PaymentRequestEncrypterTest extends TestCase
{
    /** @var EncrypterInterface&MockObject */
    private MockObject $encrypter;

    private PaymentRequestEncrypter $paymentRequestEncrypter;

    /** @var PaymentRequestInterface&MockObject */
    private MockObject $paymentRequest;

    protected function setUp(): void
    {
        $this->encrypter = $this->createMock(EncrypterInterface::class);
        $this->paymentRequestEncrypter = new PaymentRequestEncrypter($this->encrypter);
        $this->paymentRequest = $this->createMock(PaymentRequestInterface::class);
    }

    public function testAnEntityEncrypter(): void
    {
        $this->assertInstanceOf(EntityEncrypterInterface::class, $this->paymentRequestEncrypter);
    }

    public function testDoesNothingWhenEncryptingPaymentRequestWithNoPayloadAndEmptyResponseData(): void
    {
        $this->paymentRequest
            ->expects($this->once())
            ->method('getPayload')
            ->willReturn(null);
        $this->paymentRequest
            ->expects($this->once())
            ->method('getResponseData')
            ->willReturn([]);
        $this->encrypter
            ->expects($this->never())
            ->method('encrypt');
        $this->paymentRequest
            ->expects($this->once())
            ->method('setResponseData')
            ->with([]);

        $this->paymentRequestEncrypter->encrypt($this->paymentRequest);
    }

    public function testEncryptsScalarPayload(): void
    {
        $this->paymentRequest
            ->expects($this->atLeastOnce())
            ->method('getPayload')
            ->willReturn('payload');
        $this->paymentRequest
            ->expects($this->once())
            ->method('getResponseData')
            ->willReturn([]);
        $this->encrypter
            ->expects($this->once())
            ->method('encrypt')
            ->with(serialize('payload'))
            ->willReturn('encrypted_payload');
        $this->paymentRequest
            ->expects($this->once())
            ->method('setPayload')
            ->with('encrypted_payload');
        $this->paymentRequest
            ->expects($this->once())
            ->method('setResponseData')
            ->with([]);

        $this->paymentRequestEncrypter->encrypt($this->paymentRequest);
    }

    public function testEncryptsArrayPayload(): void
    {
        $this->paymentRequest
            ->expects($this->atLeastOnce())
            ->method('getPayload')
            ->willReturn(['key' => 'value']);
        $this->paymentRequest
            ->expects($this->once())
            ->method('getResponseData')
            ->willReturn([]);
        $this->encrypter
            ->expects($this->once())
            ->method('encrypt')
            ->with(serialize(['key' => 'value']))
            ->willReturn('encrypted_payload');
        $this->paymentRequest
            ->expects($this->once())
            ->method('setPayload')
            ->with('encrypted_payload');
        $this->paymentRequest
            ->expects($this->once())
            ->method('setResponseData')
            ->with([]);

        $this->paymentRequestEncrypter->encrypt($this->paymentRequest);
    }

    public function testEncryptsObjectPayload(): void
    {
        $object = new \stdClass();

        $this->paymentRequest
            ->expects($this->atLeastOnce())
            ->method('getPayload')
            ->willReturn($object);
        $this->paymentRequest
            ->expects($this->once())
            ->method('getResponseData')
            ->willReturn([]);
        $this->encrypter
            ->expects($this->once())
            ->method('encrypt')
            ->with(serialize($object))
            ->willReturn('encrypted_payload');
        $this->paymentRequest
            ->expects($this->once())
            ->method('setPayload')
            ->with('encrypted_payload');
        $this->paymentRequest
            ->expects($this->once())
            ->method('setResponseData')
            ->with([]);

        $this->paymentRequestEncrypter->encrypt($this->paymentRequest);
    }

    public function testEncryptsScalarValuesInResponseData(): void
    {
        $this->paymentRequest
            ->expects($this->once())
            ->method('getPayload')
            ->willReturn(null);
        $this->paymentRequest
            ->expects($this->once())
            ->method('getResponseData')
            ->willReturn(['key' => 'value']);
        $this->encrypter
            ->expects($this->once())
            ->method('encrypt')
            ->with(serialize('value'))
            ->willReturn('encrypted_value');
        $this->paymentRequest
            ->expects($this->never())
            ->method('setPayload')
            ->with(null);
        $this->paymentRequest
            ->expects($this->once())
            ->method('setResponseData')
            ->with(['key' => 'encrypted_value']);

        $this->paymentRequestEncrypter->encrypt($this->paymentRequest);
    }

    public function testEncryptsArrayValuesInResponseData(): void
    {
        $this->paymentRequest
            ->expects($this->once())
            ->method('getPayload')
            ->willReturn(null);
        $this->paymentRequest
            ->expects($this->once())
            ->method('getResponseData')
            ->willReturn(['key' => ['value', 'some_other_value']]);
        $this->encrypter
            ->expects($this->once())
            ->method('encrypt')
            ->with(serialize(['value', 'some_other_value']))
            ->willReturn('encrypted_value');
        $this->paymentRequest
            ->expects($this->never())
            ->method('setPayload')
            ->with(null);
        $this->paymentRequest
            ->expects($this->once())
            ->method('setResponseData')
            ->with(['key' => 'encrypted_value']);

        $this->paymentRequestEncrypter->encrypt($this->paymentRequest);
    }

    public function testDoesNothingWhenDecryptingPaymentRequestWithNoPayloadAndEmptyResponseData(): void
    {
        $this->paymentRequest
            ->expects($this->once())
            ->method('getPayload')
            ->willReturn(null);
        $this->paymentRequest
            ->expects($this->once())
            ->method('getResponseData')
            ->willReturn([]);
        $this->encrypter
            ->expects($this->never())
            ->method('decrypt');
        $this->paymentRequest
            ->expects($this->never())
            ->method('setResponseData');

        $this->paymentRequestEncrypter->decrypt($this->paymentRequest);
    }

    public function testDoesNotDecryptIfPaymentRequestIsNotString(): void
    {
        $this->paymentRequest
            ->expects($this->atLeastOnce())
            ->method('getPayload')
            ->willReturn(['array']);
        $this->paymentRequest
            ->expects($this->once())
            ->method('getResponseData')
            ->willReturn([]);
        $this->encrypter
            ->expects($this->never())
            ->method('decrypt');
        $this->paymentRequest
            ->expects($this->never())
            ->method('setResponseData');

        $this->paymentRequestEncrypter->decrypt($this->paymentRequest);
    }

    public function testDoesNotDecryptPaymentRequestIsNotEncryptedString(): void
    {
        $this->paymentRequest
            ->expects($this->atLeastOnce())
            ->method('getPayload')
            ->willReturn('not_encrypted_payload');
        $this->paymentRequest
            ->expects($this->once())
            ->method('getResponseData')
            ->willReturn([]);
        $this->encrypter
            ->expects($this->never())
            ->method('decrypt');
        $this->paymentRequest
            ->expects($this->never())
            ->method('setResponseData');

        $this->paymentRequestEncrypter->decrypt($this->paymentRequest);
    }

    public function testDecryptsScalarPayload(): void
    {
        $this->paymentRequest
            ->expects($this->atLeastOnce())
            ->method('getPayload')
            ->willReturn('encrypted_payload#ENCRYPTED');
        $this->paymentRequest
            ->expects($this->once())
            ->method('getResponseData')
            ->willReturn([]);
        $this->encrypter
            ->expects($this->once())
            ->method('decrypt')
            ->with('encrypted_payload#ENCRYPTED')
            ->willReturn(serialize('payload'));
        $this->paymentRequest
            ->expects($this->once())
            ->method('setPayload')
            ->with('payload');
        $this->paymentRequest
            ->expects($this->never())
            ->method('setResponseData');

        $this->paymentRequestEncrypter->decrypt($this->paymentRequest);
    }

    public function testDecryptsArrayPayload(): void
    {
        $this->paymentRequest
            ->expects($this->atLeastOnce())
            ->method('getPayload')
            ->willReturn('encrypted_payload#ENCRYPTED');
        $this->paymentRequest
            ->expects($this->once())
            ->method('getResponseData')
            ->willReturn([]);
        $this->encrypter
            ->expects($this->once())
            ->method('decrypt')
            ->with('encrypted_payload#ENCRYPTED')
            ->willReturn(serialize(['key' => 'value']));
        $this->paymentRequest
            ->expects($this->once())
            ->method('setPayload')
            ->with(['key' => 'value']);
        $this->paymentRequest
            ->expects($this->never())
            ->method('setResponseData');

        $this->paymentRequestEncrypter->decrypt($this->paymentRequest);
    }

    public function testDecryptsObjectPayload(): void
    {
        $object = new \stdClass();

        $this->paymentRequest
            ->expects($this->atLeastOnce())
            ->method('getPayload')
            ->willReturn('encrypted_payload#ENCRYPTED');
        $this->paymentRequest
            ->expects($this->once())
            ->method('getResponseData')
            ->willReturn([]);
        $this->encrypter
            ->expects($this->once())
            ->method('decrypt')
            ->with('encrypted_payload#ENCRYPTED')
            ->willReturn(serialize($object));
        $this->paymentRequest
            ->expects($this->once())
            ->method('setPayload')
            ->with($object);
        $this->paymentRequest
            ->expects($this->never())
            ->method('setResponseData');

        $this->paymentRequestEncrypter->decrypt($this->paymentRequest);
    }

    public function testDoesNotDecryptResponseDataWhenItsElementsAreNotStrings(): void
    {
        $this->paymentRequest
            ->expects($this->once())
            ->method('getPayload')
            ->willReturn(null);
        $this->paymentRequest
            ->expects($this->once())
            ->method('getResponseData')
            ->willReturn([
                'array' => ['value'],
                'array-two' => ['value'],
            ]);
        $this->encrypter
            ->expects($this->never())
            ->method('decrypt');
        $this->paymentRequest
            ->expects($this->never())
            ->method('setPayload')
            ->with(null);
        $this->paymentRequest
            ->expects($this->never())
            ->method('setResponseData');

        $this->paymentRequestEncrypter->decrypt($this->paymentRequest);
    }

    public function testDoesNotDecryptResponseDataWhenItsElementsAreNotEncryptedStrings(): void
    {
        $this->paymentRequest
            ->expects($this->once())
            ->method('getPayload')
            ->willReturn(null);
        $this->paymentRequest
            ->expects($this->once())
            ->method('getResponseData')
            ->willReturn([
                'key' => 'not_encrypted_value',
                'key-two' => 'not_encrypted_value',
            ]);
        $this->encrypter
            ->expects($this->never())
            ->method('decrypt');
        $this->paymentRequest
            ->expects($this->never())
            ->method('setPayload')->with(null);
        $this->paymentRequest
            ->expects($this->never())
            ->method('setResponseData');

        $this->paymentRequestEncrypter->decrypt($this->paymentRequest);
    }

    public function testDecryptsScalarValuesInResponseData(): void
    {
        $this->paymentRequest
            ->expects($this->atLeastOnce())
            ->method('getPayload')
            ->willReturn(null);
        $this->paymentRequest
            ->expects($this->atLeastOnce())
            ->method('getResponseData')
            ->willReturn(['key' => 'encrypted_value#ENCRYPTED']);
        $this->encrypter
            ->expects($this->once())
            ->method('decrypt')
            ->with('encrypted_value#ENCRYPTED')
            ->willReturn(serialize('value'));
        $this->paymentRequest
            ->expects($this->never())
            ->method('setPayload')
            ->with(null);
        $this->paymentRequest
            ->expects($this->once())
            ->method('setResponseData')
            ->with(['key' => 'value']);

        $this->paymentRequestEncrypter->decrypt($this->paymentRequest);
    }

    public function testDecryptsArrayValuesInResponseData(): void
    {
        $this->paymentRequest
            ->expects($this->atLeastOnce())
            ->method('getPayload')
            ->willReturn(null);
        $this->paymentRequest
            ->expects($this->atLeastOnce())
            ->method('getResponseData')
            ->willReturn(['key' => 'encrypted_value#ENCRYPTED']);
        $this->encrypter
            ->expects($this->once())
            ->method('decrypt')
            ->with('encrypted_value#ENCRYPTED')
            ->willReturn(serialize(['value', 'some_other_value']));
        $this->paymentRequest
            ->expects($this->never())
            ->method('setPayload')->with(null);
        $this->paymentRequest
            ->expects($this->once())
            ->method('setResponseData')
            ->with(['key' => ['value', 'some_other_value']]);

        $this->paymentRequestEncrypter->decrypt($this->paymentRequest);
    }
}
