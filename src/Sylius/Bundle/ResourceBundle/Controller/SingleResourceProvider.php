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
class SingleResourceProvider implements SingleResourceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function get(RequestConfiguration $requestConfiguration, RepositoryInterface $repository)
    {
        if (null !== $repositoryMethod = $requestConfiguration->getRepositoryMethod()) {
            $callable = [$repository, $repositoryMethod];

            if (!method_exists($repository, $repositoryMethod)) {
                throw new \InvalidArgumentException(sprintf(
                    'Method "%s" does not exist on repository of class "%s"',
                    $repositoryMethod, get_class($repository)
                ));
            }

            return call_user_func_array($callable, $requestConfiguration->getRepositoryArguments());
        }

        $criteria = [];
        $request = $requestConfiguration->getRequest();

        if ($request->attributes->has('id')) {
            $identifier = $request->attributes->get('id');
            $resource = $repository->find($identifier);

            if (!$resource) {
                throw new \InvalidArgumentException(sprintf(
                    'Could not find resource by ID "%s" from repository "%s"',
                    $identifier, get_class($repository)
                ));
            }

            return $resource;
        }

        if ($request->attributes->has('slug')) {
            $criteria = ['slug' => $request->attributes->get('slug')];
        }

        $criteria = array_merge($criteria, $requestConfiguration->getCriteria());

        $resource = $repository->findOneBy($criteria);

        if (!$resource) {
            throw new \InvalidArgumentException(sprintf(
                'Could not find resource by criteria "%s" in repository "%s"',
                json_encode($criteria), get_class($repository)
            ));
        }

        return $resource;
    }
}
