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
 * 
 * This class provides numbered pin
 */
class PinGenerator implements GeneratorInterface
{
    /**
     * {@inheritDoc}
     * @param $length has to be lowwer then 10 becouse of integer range
     */
    public function generate($length)
    {
        if ($length >= 10) {
            throw new InvalidArgumentException('The value of pin length can not be greater or equals to 10 because of an integer range. The value that you provide is'.$length);
        }

        if (!is_numeric($length)) {
            throw new InvalidArgumentException('The value of pin length has to be an integer. The value that you provide is'.$length);
        }

        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        
        return mt_rand($min, $max);
    }

}
