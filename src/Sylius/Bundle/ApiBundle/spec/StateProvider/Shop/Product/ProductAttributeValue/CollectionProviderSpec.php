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

namespace spec\Sylius\Bundle\ApiBundle\StateProvider\Shop\Product\ProductAttributeValue;

use ApiPlatform\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Sylius\Component\Product\Model\ProductAttributeValue;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Repository\ProductAttributeValueRepositoryInterface;

final class CollectionProviderSpec extends ObjectBehavior
{
    public function let(
        SectionProviderInterface $sectionProvider,
        ProductAttributeValueRepositoryInterface $attributeValueRepository,
        LocaleContextInterface $localeContext,
        LocaleProviderInterface $localeProvider,
        QueryResultCollectionExtensionInterface $extension,
    ) {
        $defaultLocaleCode = 'en_US';
        $collectionExtensions = [$extension];

        $this->beConstructedWith(
            $collectionExtensions,
            $sectionProvider,
            $attributeValueRepository,
            $localeContext,
            $localeProvider,
            $defaultLocaleCode,
        );
    }

    public function it_implements_provider_interface()
    {
        $this->shouldImplement(ProviderInterface::class);
    }

    public function it_provides_product_attributes(
        ProductAttributeValueRepositoryInterface $attributeValueRepository,
        SectionProviderInterface $sectionProvider,
        LocaleContextInterface $localeContext,
        LocaleProviderInterface $localeProvider,
        QueryBuilder $queryBuilder,
        QueryResultCollectionExtensionInterface $extension,
    ) {
        $operation = new GetCollection(class: ProductAttributeValueInterface::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $localeContext->getLocaleCode()->willReturn('en_US');
        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');

        $attributeValueRepository->createByProductCodeAndLocaleQueryBuilder('PRODUCT_CODE', 'en_US', 'en_US', 'en_US')
            ->willReturn($queryBuilder);

        $extension->applyToCollection($queryBuilder, new QueryNameGenerator(), ProductAttributeValueInterface::class, $operation, [])
            ->shouldBeCalled();

        $extension->supportsResult(ProductAttributeValueInterface::class, $operation)
            ->willReturn(true);

        $extension->getResult($queryBuilder)
            ->willReturn(new \ArrayIterator([new ProductAttributeValue()]));

        $this->provide($operation, ['code' => 'PRODUCT_CODE'], [])
            ->shouldReturnAnInstanceOf(\ArrayIterator::class);
    }

    public function it_returns_query_result_when_no_extensions_support_result(
        ProductAttributeValueRepositoryInterface $attributeValueRepository,
        SectionProviderInterface $sectionProvider,
        LocaleContextInterface $localeContext,
        LocaleProviderInterface $localeProvider,
        QueryBuilder $queryBuilder,
        AbstractQuery $query,
        QueryResultCollectionExtensionInterface $extension,
    ) {
        $operation = new GetCollection(class: ProductAttributeValueInterface::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $localeContext->getLocaleCode()->willReturn('en_US');
        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');

        $attributeValueRepository->createByProductCodeAndLocaleQueryBuilder('PRODUCT_CODE', 'en_US', 'en_US', 'en_US')
            ->willReturn($queryBuilder);

        $extension->applyToCollection($queryBuilder, new QueryNameGenerator(), ProductAttributeValueInterface::class, $operation, [])
            ->shouldBeCalled();

        $extension->supportsResult(ProductAttributeValueInterface::class, $operation)
            ->willReturn(false);

        $queryBuilder->getQuery()->willReturn($query);
        $query->getResult()->willReturn([$productAttributeValue = new ProductAttributeValue()]);

        $this->provide($operation, ['code' => 'PRODUCT_CODE'], [])
            ->shouldReturn([$productAttributeValue]);
    }

    function it_throws_an_exception_when_operation_class_is_not_product_attribute_value(
        Operation $operation,
    ): void {
        $operation->getClass()->willReturn(\stdClass::class);

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation])
        ;
    }

    function it_throws_an_exception_when_operation_is_not_get_collection(
        Operation $operation,
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation->getClass()->willReturn(ProductAttributeValueInterface::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation])
        ;
    }

    function it_throws_an_exception_when_operation_is_not_in_shop_api_section(
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new GetCollection(class: ProductAttributeValueInterface::class);
        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('provide', [$operation])
        ;
    }
}
