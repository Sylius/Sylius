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

            return call_user_func_array($callable, $requestConfiguration->getRepositoryArguments());
        }

        $criteria = [];
        $request = $requestConfiguration->getRequest();

        if ($request->attributes->has('id')) {
            $criteria = ['id' => $request->attributes->get('id')];
        }
        if ($request->attributes->has('slug')) {
            $criteria = ['slug' => $request->attributes->get('slug')];
        }

        $criteria = array_merge($criteria, $requestConfiguration->getCriteria());

        return $repository->findOneBy($criteria);
    }
}
