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

namespace Sylius\Bundle\ResourceBundle\Controller;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class SingleResourceProvider implements SingleResourceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function get(RequestConfiguration $requestConfiguration, RepositoryInterface $repository): ?ResourceInterface
    {
        $repositoryMethod = $requestConfiguration->getRepositoryMethod();
        if (null !== $repositoryMethod) {
            $arguments = array_values($requestConfiguration->getRepositoryArguments());

            return $repository->$repositoryMethod(...$arguments);
        }

        $criteria = [];
        $request = $requestConfiguration->getRequest();

        if ($request->attributes->has('id')) {
            return $repository->find($request->attributes->get('id'));
        }

        if ($request->attributes->has('slug')) {
            $criteria = ['slug' => $request->attributes->get('slug')];
        }

        $criteria = array_merge($criteria, $requestConfiguration->getCriteria());

        return $repository->findOneBy($criteria);
    }
}
