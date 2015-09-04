<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\User\Security;

use Sylius\Component\User\Model\UserInterface;

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
class UserPbkdf2PasswordEncoder implements UserPasswordEncoderInterface
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
     * @param string $algorithm          The digest algorithm to use
     * @param bool   $encodeHashAsBase64 Whether to base64 encode the password hash
     * @param int    $iterations         The number of iterations to use to stretch the password hash
     * @param int    $length             Length of derived key to create
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
    public function encode(UserInterface $user)
    {
        return $this->encodePassword($user->getPlainPassword(), $user->getSalt());
    }

    /**
     * @param string $plainPassword
     * @param string $salt
     *
     * @return string
     *
     * @throws \LogicException when the algorithm is not supported
     */
    protected function encodePassword($plainPassword, $salt)
    {
        if ($this->isPasswordTooLong($plainPassword)) {
            throw new \InvalidArgumentException('Too long password.');
        }

        if (!in_array($this->algorithm, hash_algos(), true)) {
            throw new \LogicException(sprintf('The algorithm "%s" is not supported.', $this->algorithm));
        }

        if (function_exists('hash_pbkdf2')) {
            $digest = hash_pbkdf2($this->algorithm, $plainPassword, $salt, $this->iterations, $this->length, true);
        } else {
            $digest = $this->hashPbkdf2($this->algorithm, $plainPassword, $salt, $this->iterations, $this->length);
        }

        return $this->encodeHashAsBase64 ? base64_encode($digest) : bin2hex($digest);
    }

    /**
     * @param string $password
     *
     * @return bool
     */
    protected function isPasswordTooLong($password)
    {
        return strlen($password) > self::MAX_PASSWORD_LENGTH;
    }

    /**
     * @todo remove this method after PHP 5.5 bump
     *
     * @param string $algorithm
     * @param string $password
     * @param string $salt
     * @param int    $iterations
     * @param int    $length
     *
     * @return string
     */
    private function hashPbkdf2($algorithm, $password, $salt, $iterations, $length = 0)
    {
        // Number of blocks needed to create the derived key
        $blocks = ceil($length / strlen(hash($algorithm, null, true)));
        $digest = '';

        for ($i = 1; $i <= $blocks; $i++) {
            $ib = $block = hash_hmac($algorithm, $salt.pack('N', $i), $password, true);

            // Iterations
            for ($j = 1; $j < $iterations; $j++) {
                $ib ^= ($block = hash_hmac($algorithm, $block, $password, true));
            }

            $digest .= $ib;
        }

        return substr($digest, 0, $this->length);
    }
}
