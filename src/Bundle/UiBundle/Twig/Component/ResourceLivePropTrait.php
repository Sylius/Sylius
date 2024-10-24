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

namespace Sylius\Bundle\UiBundle\Twig\Component;

use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;

/** @template T of ResourceInterface */
trait ResourceLivePropTrait
{
    /** @var RepositoryInterface<T> */
    protected RepositoryInterface $repository;

    public function hydrateResource(mixed $value): ?ResourceInterface
    {
        return $this->repository->find($value);
    }

    public function dehydrateResource(ResourceInterface|null $resource): mixed
    {
        return $resource?->getId();
    }

    /**
     * @param RepositoryInterface<T> $repository
     */
    protected function initialize(RepositoryInterface $repository): void
    {
        $this->repository = $repository;
    }
}
