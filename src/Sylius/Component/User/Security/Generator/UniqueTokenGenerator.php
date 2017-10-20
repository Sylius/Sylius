<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
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
    /**
     * @var RandomnessGeneratorInterface
     */
    private $generator;

    /**
     * @var UniquenessCheckerInterface
     */
    private $uniquenessChecker;

    /**
     * @var int
     */
    private $tokenLength;

    /**
     * @param RandomnessGeneratorInterface $generator
     * @param UniquenessCheckerInterface $uniquenessChecker
     * @param int $tokenLength
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        RandomnessGeneratorInterface $generator,
        UniquenessCheckerInterface $uniquenessChecker,
        int $tokenLength
    ) {
        Assert::greaterThanEq($tokenLength, 1, 'The value of token length has to be at least 1.');

        $this->generator = $generator;
        $this->tokenLength = $tokenLength;
        $this->uniquenessChecker = $uniquenessChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(): string
    {
        do {
            $token = $this->generator->generateUriSafeString($this->tokenLength);
        } while (!$this->uniquenessChecker->isUnique($token));

        return $token;
    }
}
