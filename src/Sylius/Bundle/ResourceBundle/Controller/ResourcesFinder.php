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
use Pagerfanta\Pagerfanta;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourcesFinder implements ResourcesFinderInterface
{
    /**
     * {@inheritdoc}
     */
    public function findCollection(RequestConfiguration $requestConfiguration, RepositoryInterface $repository)
    {
        if (null !== $factoryMethod = $requestConfiguration->getRepositoryMethod(null)) {
            $callable = array($repository, $factoryMethod);

            return call_user_func_array($callable, $requestConfiguration->getRepositoryArguments(array()));
        }

        if (!$requestConfiguration->isPaginated() && !$requestConfiguration->isLimited()) {
            return $repository->findAll();
        }

        if (!$requestConfiguration->isPaginated()) {
            return $repository->findBy($requestConfiguration->getCriteria(), $requestConfiguration->getSorting(), $requestConfiguration->getLimit());
        }

        /** @var Pagerfanta $paginator */
        $paginator = $repository->createPaginator($requestConfiguration->getCriteria(), $requestConfiguration->getSorting());

        $paginator->setCurrentPage($requestConfiguration->getRequest()->query->get('page', 1));

        return $paginator;
    }
}
