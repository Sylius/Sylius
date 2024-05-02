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

namespace Sylius\Bundle\ApiBundle\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\DataProvider\SubresourceDataProviderInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Repository\ProductAttributeValueRepositoryInterface;

final class ProductAttributesSubresourceDataProvider implements RestrictedDataProviderInterface, SubresourceDataProviderInterface
{
    public function __construct(
        private iterable $collectionExtensions,
        private ProductAttributeValueRepositoryInterface $attributeValueRepository,
        private LocaleContextInterface $localeContext,
        private LocaleProviderInterface $localeProvider,
        private string $defaultLocaleCode,
    ) {
    }

    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        $subresourceIdentifiers = $context['subresource_identifiers'] ?? null;

        return
            is_a($resourceClass, ProductAttributeValueInterface::class, true) &&
            isset($subresourceIdentifiers['code'])
        ;
    }

    public function getSubresource(string $resourceClass, array $identifiers, array $context, ?string $operationName = null)
    {
        $subresourceIdentifiers = $context['subresource_identifiers'];

        $queryBuilder = $this->attributeValueRepository->createByProductCodeAndLocaleQueryBuilder(
            $subresourceIdentifiers['code'],
            $this->localeContext->getLocaleCode(),
            $this->localeProvider->getDefaultLocaleCode(),
            $this->defaultLocaleCode,
        );

        $queryNameGenerator = new QueryNameGenerator();

        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName, $context);

            if (
                $extension instanceof QueryResultCollectionExtensionInterface &&
                $extension->supportsResult($resourceClass, $operationName)
            ) {
                return $extension->getResult($queryBuilder);
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
