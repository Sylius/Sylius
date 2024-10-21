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

namespace spec\Sylius\Component\Payment\Encryption;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Payment\Encryption\EncrypterInterface;
use Sylius\Component\Payment\Encryption\EntityEncrypterInterface;
use Sylius\Component\Payment\Encryption\PaymentRequestEncrypterInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class PaymentRequestEncrypterSpec extends ObjectBehavior
{
    function let(EncrypterInterface $encrypter): void
    {
        $this->beConstructedWith($encrypter);
    }

    function it_is_an_entity_encrypter(): void
    {
        $this->shouldImplement(EntityEncrypterInterface::class);
    }

    function it_does_nothing_when_encrypting_payment_request_with_no_payload_and_empty_response_data(
        PaymentRequestInterface $paymentRequest,
        EncrypterInterface $encrypter,
    ): void {
        $paymentRequest->getPayload()->willReturn(null);
        $paymentRequest->getResponseData()->willReturn([]);

        $encrypter->encrypt(Argument::any())->shouldNotBeCalled();

        $paymentRequest->setResponseData([])->shouldBeCalled();

        $this->encrypt($paymentRequest);
    }

    function it_encrypts_scalar_payload(
        PaymentRequestInterface $paymentRequest,
        EncrypterInterface $encrypter,
    ): void {
        $paymentRequest->getPayload()->willReturn('payload');
        $paymentRequest->getResponseData()->willReturn([]);

        $encrypter->encrypt(serialize('payload'))->willReturn('encrypted_payload');

        $paymentRequest->setPayload('encrypted_payload')->shouldBeCalled();
        $paymentRequest->setResponseData([])->shouldBeCalled();

        $this->encrypt($paymentRequest);
    }

    function it_encrypts_array_payload(
        PaymentRequestInterface $paymentRequest,
        EncrypterInterface $encrypter,
    ): void {
        $paymentRequest->getPayload()->willReturn(['key' => 'value']);
        $paymentRequest->getResponseData()->willReturn([]);

        $encrypter->encrypt(serialize(['key' => 'value']))->willReturn('encrypted_payload');

        $paymentRequest->setPayload('encrypted_payload')->shouldBeCalled();
        $paymentRequest->setResponseData([])->shouldBeCalled();

        $this->encrypt($paymentRequest);
    }

    function it_encrypts_object_payload(
        PaymentRequestInterface $paymentRequest,
        EncrypterInterface $encrypter,
    ): void {
        $object = new \stdClass();

        $paymentRequest->getPayload()->willReturn($object);
        $paymentRequest->getResponseData()->willReturn([]);

        $encrypter->encrypt(serialize($object))->willReturn('encrypted_payload');

        $paymentRequest->setPayload('encrypted_payload')->shouldBeCalled();
        $paymentRequest->setResponseData([])->shouldBeCalled();

        $this->encrypt($paymentRequest);
    }

    function it_encrypts_scalar_values_in_response_data(
        PaymentRequestInterface $paymentRequest,
        EncrypterInterface $encrypter,
    ): void {
        $paymentRequest->getPayload()->willReturn(null);
        $paymentRequest->getResponseData()->willReturn(['key' => 'value']);

        $encrypter->encrypt(serialize('value'))->willReturn('encrypted_value');

        $paymentRequest->setPayload(null)->shouldNotBeCalled();
        $paymentRequest->setResponseData(['key' => 'encrypted_value'])->shouldBeCalled();

        $this->encrypt($paymentRequest);
    }

    function it_encrypts_array_values_in_response_data(
        PaymentRequestInterface $paymentRequest,
        EncrypterInterface $encrypter,
    ): void {
        $paymentRequest->getPayload()->willReturn(null);
        $paymentRequest->getResponseData()->willReturn(['key' => ['value', 'some_other_value']]);

        $encrypter->encrypt(serialize(['value', 'some_other_value']))->willReturn('encrypted_value');

        $paymentRequest->setPayload(null)->shouldNotBeCalled();
        $paymentRequest->setResponseData(['key' => 'encrypted_value'])->shouldBeCalled();

        $this->encrypt($paymentRequest);
    }

    function it_does_nothing_when_decrypting_payment_request_with_no_payload_and_empty_response_data(
        PaymentRequestInterface $paymentRequest,
        EncrypterInterface $encrypter,
    ): void {
        $paymentRequest->getPayload()->willReturn(null);
        $paymentRequest->getResponseData()->willReturn([]);

        $encrypter->decrypt(Argument::any())->shouldNotBeCalled();

        $paymentRequest->setResponseData([])->shouldBeCalled();

        $this->decrypt($paymentRequest);
    }

    function it_decrypts_scalar_payload(
        PaymentRequestInterface $paymentRequest,
        EncrypterInterface $encrypter,
    ): void {
        $paymentRequest->getPayload()->willReturn('encrypted_payload');
        $paymentRequest->getResponseData()->willReturn([]);

        $encrypter->decrypt('encrypted_payload')->willReturn(serialize('payload'));

        $paymentRequest->setPayload('payload')->shouldBeCalled();
        $paymentRequest->setResponseData([])->shouldBeCalled();

        $this->decrypt($paymentRequest);
    }

    function it_decrypts_array_payload(
        PaymentRequestInterface $paymentRequest,
        EncrypterInterface $encrypter,
    ): void {
        $paymentRequest->getPayload()->willReturn('encrypted_payload');
        $paymentRequest->getResponseData()->willReturn([]);

        $encrypter->decrypt('encrypted_payload')->willReturn(serialize(['key' => 'value']));

        $paymentRequest->setPayload(['key' => 'value'])->shouldBeCalled();
        $paymentRequest->setResponseData([])->shouldBeCalled();

        $this->decrypt($paymentRequest);
    }

    function it_decrypts_object_payload(
        PaymentRequestInterface $paymentRequest,
        EncrypterInterface $encrypter,
    ): void {
        $paymentRequest->getPayload()->willReturn('encrypted_payload');
        $paymentRequest->getResponseData()->willReturn([]);

        $object = new \stdClass();

        $encrypter->decrypt('encrypted_payload')->willReturn(serialize($object));

        $paymentRequest->setPayload($object)->shouldBeCalled();
        $paymentRequest->setResponseData([])->shouldBeCalled();

        $this->decrypt($paymentRequest);
    }

    function it_decrypts_scalar_values_in_response_data(
        PaymentRequestInterface $paymentRequest,
        EncrypterInterface $encrypter,
    ): void {
        $paymentRequest->getPayload()->willReturn(null);
        $paymentRequest->getResponseData()->willReturn(['key' => 'encrypted_value']);

        $encrypter->decrypt('encrypted_value')->willReturn(serialize('value'));

        $paymentRequest->setPayload(null)->shouldNotBeCalled();
        $paymentRequest->setResponseData(['key' => 'value'])->shouldBeCalled();

        $this->decrypt($paymentRequest);
    }

    function it_decrypts_array_values_in_response_data(
        PaymentRequestInterface $paymentRequest,
        EncrypterInterface $encrypter,
    ): void {
        $paymentRequest->getPayload()->willReturn(null);
        $paymentRequest->getResponseData()->willReturn(['key' => 'encrypted_value']);

        $encrypter->decrypt('encrypted_value')->willReturn(serialize(['value', 'some_other_value']));

        $paymentRequest->setPayload(null)->shouldNotBeCalled();
        $paymentRequest->setResponseData(['key' => ['value', 'some_other_value']])->shouldBeCalled();

        $this->decrypt($paymentRequest);
    }
}
