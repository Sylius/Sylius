<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\TokenAssigner;

/**
 * Solution taken from here: http://stackoverflow.com/a/13733588/1056679
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class UniqueTokenGenerator
{
    private $alphabet;
    private $alphabetLength;

    public function __construct()
    {
        $this->alphabet =
            implode(range('a', 'z'))
            .implode(range('A', 'Z'))
            .implode(range(0, 9))
        ;

        $this->alphabetLength = strlen($this->alphabet);
    }

    /**
    * @param int $length
    * @return string
    */
    public function generate($length)
    {
        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $randomKey = $this->getRandomInteger(0, $this->alphabetLength);
            $token .= $this->alphabet[$randomKey];
        }

        return $token;
    }

    /**
    * @param int $min
    * @param int $max
     *
    * @return int
    */
    private function getRandomInteger($min, $max)
    {
        $range = ($max - $min);

        if ($range < 0) {
            return $min;
        }

        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1;
        $bits = (int) $log + 1;
        $filter = (int) (1 << $bits) - 1;

        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter;

        } while ($rnd >= $range);

        return ($min + $rnd);
    }
}
