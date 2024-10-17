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

namespace Sylius\Component\User\Security\Generator;

use Sylius\Component\User\Security\Checker\UniquenessCheckerInterface;
use Sylius\Resource\Generator\RandomnessGeneratorInterface;
use Webmozart\Assert\Assert;

trigger_deprecation(
    'sylius/user',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0.',
    UniquePinGenerator::class,
);

/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. */
final class UniquePinGenerator implements GeneratorInterface
{
    private int $pinLength;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(
        private RandomnessGeneratorInterface $generator,
        private UniquenessCheckerInterface $uniquenessChecker,
        int $pinLength,
    ) {
        Assert::greaterThanEq($pinLength, 1, 'The value of token length has to be at least 1.');
        $this->pinLength = $pinLength;
    }

    public function generate(): string
    {
        do {
            $pin = $this->generator->generateNumeric($this->pinLength);
        } while (!$this->uniquenessChecker->isUnique($pin));

        return $pin;
    }
}
