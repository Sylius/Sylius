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

namespace spec\Sylius\Bundle\ApiBundle\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Repository\ProductAttributeValueRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class ProductAttributesSubresourceDataProviderSpec extends ObjectBehavior
{
    function let(
        ProductAttributeValueRepositoryInterface $attributeValueRepository,
        LocaleContextInterface $localeContext,
        LocaleProviderInterface $localeProvider,
    ): void {
        $this->beConstructedWith([], $attributeValueRepository, $localeContext, $localeProvider, 'pl_PL');
    }

    function it_supports_only_product_attributes_subresource_data_provider(): void
    {
        $context = [
            'subresource_identifiers' => ['code' => 'PRODUCT_CODE'],
        ];

        $this
            ->supports(ProductInterface::class, Request::METHOD_GET, $context)
            ->shouldReturn(false)
        ;

        $this
            ->supports(ProductAttributeValueInterface::class, Request::METHOD_GET, $context)
            ->shouldReturn(true)
        ;
    }

    function it_returns_product_attributes_without_query_extensions(
        ProductAttributeValueRepositoryInterface $attributeValueRepository,
        LocaleContextInterface $localeContext,
        LocaleProviderInterface $localeProvider,
        QueryBuilder $queryBuilder,
        AbstractQuery $query,
    ): void {
        $localeContext->getLocaleCode()->willReturn('en_US');
        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');

        $attributeValueRepository
            ->createByProductCodeAndLocaleQueryBuilder(
                'xyz',
                'en_US',
                'en_US',
                'pl_PL',
            )
            ->willReturn($queryBuilder)
        ;

        $queryBuilder->getQuery()->willReturn($query);
        $query->getResult()->willReturn(['key' => 'value']);

        $this
            ->getSubresource(
                'resourceClass',
                [],
                [
                    'subresource_identifiers' => ['code' => 'xyz'],
                ],
            )
            ->shouldReturn(['key' => 'value'])
        ;
    }

    function it_should_not_call_the_second_query_extension_if_the_first_one_is_supported_query_result_collection_extension(
        ProductAttributeValueRepositoryInterface $attributeValueRepository,
        LocaleContextInterface $localeContext,
        LocaleProviderInterface $localeProvider,
        QueryBuilder $queryBuilder,
        QueryResultCollectionExtensionInterface $firstQueryResultCollectionExtension,
        QueryResultCollectionExtensionInterface $secondQueryResultCollectionExtension,
    ): void {
        $this->beConstructedWith(
            [$firstQueryResultCollectionExtension, $secondQueryResultCollectionExtension],
            $attributeValueRepository,
            $localeContext,
            $localeProvider,
            'pl_PL',
        );

        $context = [
            'subresource_identifiers' => ['code' => 'xyz'],
        ];

        $localeContext->getLocaleCode()->willReturn('en_US');
        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');

        $attributeValueRepository
            ->createByProductCodeAndLocaleQueryBuilder(
                'xyz',
                'en_US',
                'en_US',
                'pl_PL',
            )
            ->willReturn($queryBuilder)
        ;

        $firstQueryResultCollectionExtension
            ->applyToCollection($queryBuilder, Argument::type(QueryNameGenerator::class), 'resourceClass', null, $context)
            ->shouldBeCalled()
        ;

        $firstQueryResultCollectionExtension
            ->supportsResult('resourceClass', null)
            ->willReturn(true)
        ;

        $firstQueryResultCollectionExtension->getResult($queryBuilder)->willReturn(['key' => 'value']);

        $secondQueryResultCollectionExtension->applyToCollection(Argument::any())->shouldNotBeCalled();

        $this
            ->getSubresource(
                'resourceClass',
                [],
                $context,
            )
            ->shouldReturn(['key' => 'value'])
        ;
    }

    function it_returns_product_attributes_directly_from_query_builder_if_there_are_only_unsupported_query_result_collection_extensions(
        ProductAttributeValueRepositoryInterface $attributeValueRepository,
        LocaleContextInterface $localeContext,
        LocaleProviderInterface $localeProvider,
        QueryBuilder $queryBuilder,
        AbstractQuery $query,
        QueryCollectionExtensionInterface $queryCollectionExtension,
        QueryResultCollectionExtensionInterface $queryResultCollectionExtension,
    ): void {
        $this->beConstructedWith(
            [$queryCollectionExtension, $queryResultCollectionExtension],
            $attributeValueRepository,
            $localeContext,
            $localeProvider,
            'pl_PL',
        );

        $context = [
            'subresource_identifiers' => ['code' => 'xyz'],
        ];

        $localeContext->getLocaleCode()->willReturn('en_US');
        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');

        $attributeValueRepository
            ->createByProductCodeAndLocaleQueryBuilder(
                'xyz',
                'en_US',
                'en_US',
                'pl_PL',
            )
            ->willReturn($queryBuilder)
        ;

        $queryCollectionExtension
            ->applyToCollection($queryBuilder, Argument::type(QueryNameGenerator::class), 'resourceClass', null, $context)
            ->shouldBeCalled()
        ;

        $queryResultCollectionExtension
            ->applyToCollection($queryBuilder, Argument::type(QueryNameGenerator::class), 'resourceClass', null, $context)
            ->shouldBeCalled()
        ;

        $queryResultCollectionExtension
            ->supportsResult('resourceClass', null)
            ->willReturn(false)
        ;

        $queryBuilder->getQuery()->willReturn($query);
        $query->getResult()->willReturn(['key' => 'value']);

        $this
            ->getSubresource(
                'resourceClass',
                [],
                $context,
            )
            ->shouldReturn(['key' => 'value'])
        ;
    }
}
