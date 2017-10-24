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

namespace Sylius\Component\User\Security\Checker;

use Sylius\Component\Resource\Repository\RepositoryInterface;

final class TokenUniquenessChecker implements UniquenessCheckerInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var string
     */
    private $tokenFieldName;

    /**
     * @param RepositoryInterface $repository
     * @param string $tokenFieldName
     */
    public function __construct(RepositoryInterface $repository, string $tokenFieldName)
    {
        $this->repository = $repository;
        $this->tokenFieldName = $tokenFieldName;
    }

    /**
     * {@inheritdoc}
     */
    public function isUnique(string $token): bool
    {
        return null === $this->repository->findOneBy([$this->tokenFieldName => $token]);
    }
}
