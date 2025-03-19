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
use Sylius\Component\Payment\Encryption\EncrypterInterface;
use Sylius\Component\Payment\Encryption\Exception\EncryptionException;

final class EncrypterSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(__DIR__ . '/fixtures/encryption_key');
    }

    function it_is_an_encrypter(): void
    {
        $this->shouldImplement(EncrypterInterface::class);
    }

    function it_throws_an_exception_if_it_cannot_encrypt(): void
    {
        $this->beConstructedWith('');
        $this->shouldThrow(EncryptionException::class)->during('encrypt', ['data']);
    }

    function it_throws_an_exception_if_it_cannot_decrypt(): void
    {
        $this->beConstructedWith('');
        $this->shouldThrow(EncryptionException::class)->during('decrypt', ['data#ENCRYPTED']);
    }

    function it_encrypts_data(): void
    {
        $this->encrypt('data')->shouldBeString();
        $this->encrypt('data')->shouldNotBe('data');
        $this->encrypt('data')->shouldEndWith('#ENCRYPTED');
    }

    function it_decrypts_data(): void
    {
        $data = 'data';
        $encryptedData = $this->getWrappedObject()->encrypt($data);

        $this->decrypt($encryptedData)->shouldNotEndWith('#ENCRYPTED');
        $this->decrypt($encryptedData)->shouldBe($data);
    }

    function it_does_nothing_when_data_is_not_marked_as_encrypted(): void
    {
        $this->decrypt('data')->shouldBe('data');
    }
}
