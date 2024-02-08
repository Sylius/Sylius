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

use Sylius\Component\Resource\Generator\RandomnessGeneratorInterface;
use Sylius\Component\User\Security\Checker\UniquenessCheckerInterface;
use Webmozart\Assert\Assert;

final class UniqueTokenGenerator implements GeneratorInterface
{
    private int $tokenLength;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(
        private RandomnessGeneratorInterface $generator,
        private UniquenessCheckerInterface $uniquenessChecker,
        int $tokenLength,
    ) {
        Assert::greaterThanEq($tokenLength, 1, 'The value of token length has to be at least 1.');
        $this->tokenLength = $tokenLength;
    }

    public function generate(): string
    {
        do {
            $token = $this->generator->generateUriSafeString($this->tokenLength);
        } while (!$this->uniquenessChecker->isUnique($token));

        return $token;
    }
}
