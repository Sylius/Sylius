<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;

use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ResourcesResolver implements ResourcesResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function getResources(RequestConfiguration $requestConfiguration, RepositoryInterface $repository)
    {
        $repositoryMethod = $requestConfiguration->getRepositoryMethod();
        if (null !== $repositoryMethod) {
            $arguments = array_values($requestConfiguration->getRepositoryArguments());

            return $repository->$repositoryMethod(...$arguments);
        }

        if (!$requestConfiguration->isPaginated() && !$requestConfiguration->isLimited()) {
            return $repository->findAll();
        }

        if (!$requestConfiguration->isPaginated()) {
            return $repository->findBy($requestConfiguration->getCriteria(), $requestConfiguration->getSorting(), $requestConfiguration->getLimit());
        }

        return $repository->createPaginator($requestConfiguration->getCriteria(), $requestConfiguration->getSorting());
    }
}
