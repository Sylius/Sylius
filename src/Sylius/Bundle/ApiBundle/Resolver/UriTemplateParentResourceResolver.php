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

namespace Sylius\Bundle\ApiBundle\Resolver;

use ApiPlatform\Metadata\HttpOperation;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @template T of ResourceInterface
 *
 * @implements UriTemplateParentResourceResolverInterface<T>
 */
readonly class UriTemplateParentResourceResolver implements UriTemplateParentResourceResolverInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function resolve(ResourceInterface $item, HttpOperation $operation, array $context = []): ResourceInterface
    {
        if (empty($context['uri_variables'])) {
            throw new \RuntimeException('Missing URI variables to resolve parent resource.');
        }

        foreach ($operation->getUriVariables() as $variable) {
            $uriVariableClass = $variable->getFromClass();

            if ($uriVariableClass === null) {
                throw new \RuntimeException('URI variable class is not defined.');
            }

            if ($item instanceof $uriVariableClass) {
                continue;
            }

            /** @var EntityRepository<T> $parentResourceRepository */
            $parentResourceRepository = $this->entityManager->getRepository($variable->getFromClass());

            $parentItem = $parentResourceRepository->findOneBy(
                ['code' => $context['uri_variables'][$variable->getParameterName()]],
            );

            if ($parentItem === null) {
                throw new NotFoundHttpException('Parent resource not found.');
            }

            return $parentItem;
        }

        throw new \RuntimeException('Any uri variable did not match.');
    }
}
