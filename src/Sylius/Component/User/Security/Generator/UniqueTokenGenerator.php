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

use Sylius\Component\Resource\Generator\RandomnessGeneratorInterface;
use Sylius\Component\User\Security\Checker\UniquenessCheckerInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
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
        $tokenLength
    ) {
        Assert::integer(
            $tokenLength,
            'The value of token length has to be an integer.'
        );
        Assert::range(
            $tokenLength,
            1, 40,
            'The value of token length has to be in range between 1 to 40.'
        );

        $this->generator = $generator;
        $this->tokenLength = $tokenLength;
        $this->uniquenessChecker = $uniquenessChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        do {
            $token = $this->generator->generateUriSafeString($this->tokenLength);
        } while (!$this->uniquenessChecker->isUnique($token));

        return $token;
    }
}
