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
use Sylius\Component\Payment\Model\GatewayConfigInterface;

final class GatewayConfigEncrypterSpec extends ObjectBehavior
{
    function let(EncrypterInterface $encrypter): void
    {
        $this->beConstructedWith($encrypter);
    }

    function it_is_an_entity_encrypter(): void
    {
        $this->shouldImplement(EntityEncrypterInterface::class);
    }

    function it_does_nothing_when_encrypting_empty_gateway_config(
        EncrypterInterface $encrypter,
        GatewayConfigInterface $gatewayConfig,
    ): void {
        $gatewayConfig->getConfig()->willReturn([]);

        $encrypter->encrypt(Argument::any())->shouldNotBeCalled();

        $gatewayConfig->setConfig([])->shouldBeCalled();

        $this->encrypt($gatewayConfig);
    }

    function it_encrypts_scalar_values_in_gateway_config(
        EncrypterInterface $encrypter,
        GatewayConfigInterface $gatewayConfig,
    ): void {
        $gatewayConfig->getConfig()->willReturn(['key' => 'value']);

        $encrypter->encrypt(serialize('value'))->willReturn('encrypted_value');

        $gatewayConfig->setConfig(['key' => 'encrypted_value'])->shouldBeCalled();

        $this->encrypt($gatewayConfig);
    }

    function it_encrypts_array_values_in_gateway_config(
        EncrypterInterface $encrypter,
        GatewayConfigInterface $gatewayConfig,
    ): void {
        $gatewayConfig->getConfig()->willReturn(['key' => ['value', 'some_other_value']]);

        $encrypter->encrypt(serialize(['value', 'some_other_value']))->willReturn('encrypted_value');

        $gatewayConfig->setConfig(['key' => 'encrypted_value'])->shouldBeCalled();

        $this->encrypt($gatewayConfig);
    }

    function it_does_nothing_when_decrypting_empty_gateway_config(
        EncrypterInterface $encrypter,
        GatewayConfigInterface $gatewayConfig,
    ): void {
        $gatewayConfig->getConfig()->willReturn([]);

        $encrypter->decrypt(Argument::any())->shouldNotBeCalled();

        $gatewayConfig->setConfig(Argument::any())->shouldNotBeCalled();

        $this->decrypt($gatewayConfig);
    }

    function it_does_not_decrypt_config_when_its_elements_are_not_encrypted_strings(
        EncrypterInterface $encrypter,
        GatewayConfigInterface $gatewayConfig,
    ): void {
        $gatewayConfig->getConfig()->willReturn([
            'key' => 'not_encrypted_value',
            'key-two' => 'not_encrypted_value',
        ]);

        $encrypter->decrypt(Argument::any())->shouldNotBeCalled();

        $gatewayConfig->setConfig(Argument::any())->shouldNotBeCalled();

        $this->decrypt($gatewayConfig);
    }

    function it_decrypts_scalar_values_in_gateway_config(
        EncrypterInterface $encrypter,
        GatewayConfigInterface $gatewayConfig,
    ): void {
        $gatewayConfig->getConfig()->willReturn(['key' => 'encrypted_value#ENCRYPTED']);

        $encrypter->decrypt('encrypted_value#ENCRYPTED')->willReturn(serialize('value'));

        $gatewayConfig->setConfig(['key' => 'value'])->shouldBeCalled();

        $this->decrypt($gatewayConfig);
    }

    function it_decrypts_array_values_in_gateway_config(
        EncrypterInterface $encrypter,
        GatewayConfigInterface $gatewayConfig,
    ): void {
        $gatewayConfig->getConfig()->willReturn([
            'key' => 'encrypted_value#ENCRYPTED',
            'key-two' => 'encrypted_value-two#ENCRYPTED',
        ]);

        $encrypter->decrypt('encrypted_value#ENCRYPTED')->willReturn(serialize(['value', 'some_other_value']));
        $encrypter->decrypt('encrypted_value-two#ENCRYPTED')->willReturn(serialize('TWO'));

        $gatewayConfig->setConfig(['key' => ['value', 'some_other_value'], 'key-two' => 'TWO'])->shouldBeCalled();

        $this->decrypt($gatewayConfig);
    }
}
