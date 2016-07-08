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
final class UniqueTokenGenerator implements GeneratorInterface
{
    /**
     * @var UniquenessCheckerInterface
     */
    protected $uniquenessChecker;

    /**
     * @var int
     */
    protected $tokenLength;

    /**
     * @param UniquenessCheckerInterface $uniquenessChecker
     * @param int $tokenLength
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(UniquenessCheckerInterface $uniquenessChecker, $tokenLength)
    {
        Assert::integer(
            $tokenLength,
            'The value of token length has to be an integer.'
        );
        Assert::range(
            $tokenLength,
            1, 40,
            'The value of token length has to be in range between 1 to 40.'
        );

        $this->tokenLength = $tokenLength;
        $this->uniquenessChecker = $uniquenessChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        do {
            $token = $this->getRandomToken();
        } while (!$this->uniquenessChecker->isUnique($token));

        return $token;
    }

    /**
     * @return string
     */
    private function getRandomToken()
    {
        $hash = sha1(microtime(true));
        $startPosition = mt_rand(0, 40 - $this->tokenLength);

        return substr($hash, $startPosition, $this->tokenLength);
    }
}
