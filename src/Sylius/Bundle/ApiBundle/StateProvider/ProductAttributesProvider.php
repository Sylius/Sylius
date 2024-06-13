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

use ApiPlatform\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Sylius\Component\Product\Model\ProductAttributeValue;
use Sylius\Component\Product\Repository\ProductAttributeValueRepositoryInterface;

/**
 * @implements ProviderInterface<ProductAttributeValue>
 */
final readonly class ProductAttributesProvider implements ProviderInterface
{
    public function __construct(
        private iterable $collectionExtensions,
        private ProductAttributeValueRepositoryInterface $attributeValueRepository,
        private LocaleContextInterface $localeContext,
        private LocaleProviderInterface $localeProvider,
        private string $defaultLocaleCode,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|object|null
    {
        $queryBuilder = $this->attributeValueRepository->createByProductCodeAndLocaleQueryBuilder(
            $uriVariables['code'],
            $this->localeContext->getLocaleCode(),
            $this->localeProvider->getDefaultLocaleCode(),
            $this->defaultLocaleCode,
        );

        $resourceClass = $operation->getClass();
        $queryNameGenerator = new QueryNameGenerator();

        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection(
                $queryBuilder,
                $queryNameGenerator,
                $resourceClass,
                $operation,
                $context,
            );

            if (
                $extension instanceof QueryResultCollectionExtensionInterface &&
                $extension->supportsResult($resourceClass, $operation)
            ) {
                return $extension->getResult($queryBuilder)->getIterator();
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
