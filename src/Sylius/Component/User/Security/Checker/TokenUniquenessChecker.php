<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\User\Security\Checker;

use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
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
    public function __construct($repository, $tokenFieldName)
    {
        $this->repository = $repository;
        $this->tokenFieldName = $tokenFieldName;
    }

    /**
     * {@inheritdoc}
     */
    public function isUnique($token)
    {
        return null === $this->repository->findOneBy([$this->tokenFieldName => $token]);
    }
}
