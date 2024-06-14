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

namespace spec\Sylius\Bundle\ApiBundle\StateProvider;

use ApiPlatform\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\StateProvider\ProductAttributesProvider;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Sylius\Component\Product\Model\ProductAttributeValue;
use Sylius\Component\Product\Repository\ProductAttributeValueRepositoryInterface;

final class ProductAttributesProviderSpec extends ObjectBehavior
{
    public function let(
        ProductAttributeValueRepositoryInterface $attributeValueRepository,
        LocaleContextInterface $localeContext,
        LocaleProviderInterface $localeProvider,
        QueryResultCollectionExtensionInterface $extension,
    ) {
        $defaultLocaleCode = 'en_US';
        $collectionExtensions = [$extension];

        $this->beConstructedWith(
            $collectionExtensions,
            $attributeValueRepository,
            $localeContext,
            $localeProvider,
            $defaultLocaleCode,
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ProductAttributesProvider::class);
    }

    public function it_implements_provider_interface()
    {
        $this->shouldImplement(ProviderInterface::class);
    }

    public function it_provides_product_attributes(
        ProductAttributeValueRepositoryInterface $attributeValueRepository,
        LocaleContextInterface $localeContext,
        LocaleProviderInterface $localeProvider,
        QueryBuilder $queryBuilder,
        Operation $operation,
        QueryResultCollectionExtensionInterface $extension,
    ) {
        $localeContext->getLocaleCode()->willReturn('en_US');
        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');

        $attributeValueRepository->createByProductCodeAndLocaleQueryBuilder('PRODUCT_CODE', 'en_US', 'en_US', 'en_US')
            ->willReturn($queryBuilder);

        $operation->getClass()->willReturn(ProductAttributeValue::class);

        $extension->applyToCollection($queryBuilder, new QueryNameGenerator(), ProductAttributeValue::class, $operation, [])
            ->shouldBeCalled();

        $extension->supportsResult(ProductAttributeValue::class, $operation)
            ->willReturn(true);

        $extension->getResult($queryBuilder)
            ->willReturn(new \ArrayIterator([new ProductAttributeValue()]));

        $this->provide($operation, ['code' => 'PRODUCT_CODE'], [])
            ->shouldReturnAnInstanceOf(\ArrayIterator::class);
    }

    public function it_returns_query_result_when_no_extensions_support_result(
        ProductAttributeValueRepositoryInterface $attributeValueRepository,
        LocaleContextInterface $localeContext,
        LocaleProviderInterface $localeProvider,
        QueryBuilder $queryBuilder,
        AbstractQuery $query,
        Operation $operation,
        QueryResultCollectionExtensionInterface $extension,
    ) {
        $localeContext->getLocaleCode()->willReturn('en_US');
        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');

        $attributeValueRepository->createByProductCodeAndLocaleQueryBuilder('PRODUCT_CODE', 'en_US', 'en_US', 'en_US')
            ->willReturn($queryBuilder);

        $operation->getClass()->willReturn(ProductAttributeValue::class);

        $extension->applyToCollection($queryBuilder, new QueryNameGenerator(), ProductAttributeValue::class, $operation, [])
            ->shouldBeCalled();

        $extension->supportsResult(ProductAttributeValue::class, $operation)
            ->willReturn(false);

        $queryBuilder->getQuery()->willReturn($query);
        $query->getResult()->willReturn([$productAttributeValue = new ProductAttributeValue()]);

        $this->provide($operation, ['code' => 'PRODUCT_CODE'], [])
            ->shouldReturn([$productAttributeValue]);
    }
}
