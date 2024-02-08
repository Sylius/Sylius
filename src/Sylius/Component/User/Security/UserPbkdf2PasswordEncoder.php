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

namespace Sylius\Component\User\Security;

use Sylius\Component\User\Model\CredentialsHolderInterface;
use Webmozart\Assert\Assert;

/**
 * Pbkdf2PasswordEncoder uses the PBKDF2 (Password-Based Key Derivation Function 2).
 *
 * Providing a high level of Cryptographic security,
 *  PBKDF2 is recommended by the National Institute of Standards and Technology (NIST).
 *
 * But also warrants a warning, using PBKDF2 (with a high number of iterations) slows down the process.
 * PBKDF2 should be used with caution and care.
 */
final class UserPbkdf2PasswordEncoder implements UserPasswordEncoderInterface
{
    private const MAX_PASSWORD_LENGTH = 4096;

    private string $algorithm;

    private bool $encodeHashAsBase64;

    private int $iterations;

    private int $length;

    /**
     * @param int|null $length of the result of encoding
     */
    public function __construct(
        ?string $algorithm = null,
        ?bool $encodeHashAsBase64 = null,
        ?int $iterations = null,
        ?int $length = null,
    ) {
        $this->algorithm = $algorithm ?? 'sha512';
        $this->encodeHashAsBase64 = $encodeHashAsBase64 ?? true;
        $this->iterations = $iterations ?? 1000;
        $this->length = $length ?? 40;
    }

    /**
     * @throws \LogicException when the algorithm is not supported
     */
    public function encode(CredentialsHolderInterface $user): string
    {
        return $this->encodePassword($user->getPlainPassword(), $user->getSalt());
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \LogicException when the algorithm is not supported
     */
    private function encodePassword(string $plainPassword, string $salt): string
    {
        Assert::lessThanEq(
            strlen($plainPassword),
            self::MAX_PASSWORD_LENGTH,
            sprintf('The password must be at most %d characters long.', self::MAX_PASSWORD_LENGTH),
        );

        if (!in_array($this->algorithm, hash_algos(), true)) {
            throw new \LogicException(sprintf('The algorithm "%s" is not supported.', $this->algorithm));
        }

        $digest = hash_pbkdf2($this->algorithm, $plainPassword, $salt, $this->iterations, $this->length, true);

        return $this->encodeHashAsBase64 ? base64_encode($digest) : bin2hex($digest);
    }
}
