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
 */
class TokenGenerator implements GeneratorInterface
{
    /**
     * {@inheritDoc}
     */
    public function generate($length)
    {
        if (!is_numeric($length)) {
            throw new InvalidArgumentException('The value of token length has to be an integer. The value that you provide is'.$length);
        }

        $hash = sha1(microtime(true));
        return substr($hash, mt_rand(0, 33), $length);
    }

}
