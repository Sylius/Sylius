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

use Sylius\Component\User\Security\Checker\UniquenessCheckerInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class UniquePinGenerator implements GeneratorInterface
{
    /**
     * @var UniquenessCheckerInterface
     */
    private $uniquenessChecker;

    /**
     * @var int
     */
    private $pinLength;

    /**
     * @param UniquenessCheckerInterface $uniquenessChecker
     * @param int $pinLength
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(UniquenessCheckerInterface $uniquenessChecker, $pinLength)
    {
        Assert::integer(
            $pinLength,
            'The value of pin length has to be an integer.'
        );
        Assert::range(
            $pinLength,
            1, 9,
            'The value of pin length has to be in range between 1 to 9.'
        );

        $this->pinLength = $pinLength;
        $this->uniquenessChecker = $uniquenessChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        do {
            $pin = $this->getRandomPin();
        } while (!$this->uniquenessChecker->isUnique($pin));

        return $pin;
    }

    /**
     * @return string
     */
    private function getRandomPin()
    {
        $max = pow(10, $this->pinLength) - 1;

        return str_pad(((string) mt_rand(0, $max) - 1), $this->pinLength, '0', STR_PAD_LEFT);
    }
}
