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

namespace Sylius\Bundle\ApiBundle\StateProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\RecursiveAdjustmentsAwareInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final readonly class RecursiveAdjustmentsStateProvider implements ProviderInterface
{
    public function __construct(
        private RepositoryInterface $repository,
        private string $identifier,
    ) {
        $classname = $this->repository->getClassName();
        if (false === is_a($classname, RecursiveAdjustmentsAwareInterface::class, true)) {
            throw new \LogicException(
                sprintf('Class "%s" does not implement "%s".', $classname, RecursiveAdjustmentsAwareInterface::class)
            );
        }
    }

    /** @return Collection<AdjustmentInterface> */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Collection
    {
        $identifier = $uriVariables[$this->identifier] ?? null;
        if (null === $identifier) {
            throw new \RuntimeException('No identifier provided in `uri_variables`.');
        }

        /** @var RecursiveAdjustmentsAwareInterface|null $adjustable */
        $adjustable = $this->repository->findOneBy([$this->identifier => $identifier]);
        if (null === $adjustable) {
            throw new \RuntimeException(sprintf('Adjustable with %s="%s" not found.', $this->identifier, $identifier));
        }

        return $adjustable->getAdjustmentsRecursively($context['request']->query->get('type'));
    }
}
