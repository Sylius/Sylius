<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\User\Security;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class TokenProvider implements TokenProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var GeneratorInterface
     */
    private $generator;

    /**
     * @var int
     */
    private $tokenLength;

    /**
     * @param RepositoryInterface    $repository
     * @param EntityManagerInterface $manager
     * @param GeneratorInterface     $generator
     * @param int                    $tokenLength
     */
    public function __construct(RepositoryInterface $repository, EntityManagerInterface $manager, GeneratorInterface $generator, $tokenLength)
    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->generator = $generator;
        $this->tokenLength = (int) $tokenLength;
    }

    /**
     * {@inheritdoc}
     */
    public function generateUniqueToken()
    {
        do {
            $token = $this->generator->generate($this->tokenLength);
        } while ($this->isUsedCode($token));

        return $token;
    }

    /**
     * @param string $token
     *
     * @return bool
     */
    protected function isUsedCode($token)
    {
        return null !== $this->repository->findOneBy(['confirmationToken' => $token]);
    }
}
