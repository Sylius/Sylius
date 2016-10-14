<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Andrew Johnson
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
final class UserPbkdf2PasswordEncoder implements UserPasswordEncoderInterface
{
    const MAX_PASSWORD_LENGTH = 4096;

    /**
     * @var string
     */
    private $algorithm;

    /**
     * @var bool
     */
    private $encodeHashAsBase64;

    /**
     * @var int
     */
    private $iterations;

    /**
     * @var int
     */
    private $length;

    /**
     * @param string $algorithm
     * @param bool $encodeHashAsBase64
     * @param int $iterations
     * @param int $length of the result of encoding
     */
    public function __construct($algorithm = 'sha512', $encodeHashAsBase64 = true, $iterations = 1000, $length = 40)
    {
        $this->algorithm = $algorithm;
        $this->encodeHashAsBase64 = $encodeHashAsBase64;
        $this->iterations = $iterations;
        $this->length = $length;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException when the algorithm is not supported
     */
    public function encode(CredentialsHolderInterface $user)
    {
        return $this->encodePassword($user->getPlainPassword(), $user->getSalt());
    }

    /**
     * @param string $plainPassword
     * @param string $salt
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     * @throws \LogicException when the algorithm is not supported
     */
    private function encodePassword($plainPassword, $salt)
    {
        Assert::lessThanEq(
            strlen($plainPassword),
            self::MAX_PASSWORD_LENGTH,
            sprintf('The password must be at most %d characters long.', self::MAX_PASSWORD_LENGTH)
        );

        if (!in_array($this->algorithm, hash_algos(), true)) {
            throw new \LogicException(sprintf('The algorithm "%s" is not supported.', $this->algorithm));
        }

        $digest = hash_pbkdf2($this->algorithm, $plainPassword, $salt, $this->iterations, $this->length, true);

        return $this->encodeHashAsBase64 ? base64_encode($digest) : bin2hex($digest);
    }
}
