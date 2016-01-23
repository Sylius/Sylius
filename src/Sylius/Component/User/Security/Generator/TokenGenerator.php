<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\User\Security\Generator;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class TokenGenerator implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function generate($length)
    {
        if (!is_int($length)) {
            throw new \InvalidArgumentException('The value of token length has to be an integer.');
        }

        if ((0 >= $length) || (40 < $length)) {
            throw new \InvalidArgumentException('The value of token length has to be in range between 1 to 40.');
        }

        $hash = sha1(microtime(true));
        // 40 is a length of sha1
        $startPosition = min(40 - $length, mt_rand(0, 33));

        return substr($hash, $startPosition, $length);
    }
}
