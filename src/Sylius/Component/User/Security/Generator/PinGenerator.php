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
class PinGenerator implements GeneratorInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function generate($length)
    {
        if (!is_int($length)) {
            throw new \InvalidArgumentException('The value of pin length has to be an integer.');
        }

        if ((0 >= $length) || (10 <= $length)) {
            throw new \InvalidArgumentException('The value of pin length has to be in range between 1 to 9.');
        }

        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;

        return (string) mt_rand($min, $max);
    }
}
