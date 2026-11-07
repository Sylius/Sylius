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

namespace Tests\Sylius\Bundle\CoreBundle\CatalogPromotion\Processor;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandDispatcher\ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\AllProductVariantsCatalogPromotionsProcessorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\TargetedCatalogPromotionProcessor;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\TargetedCatalogPromotionProcessorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Provider\VariantCodesInScopeProviderInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Provider\VariantCodesWithAppliedPromotionProviderInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

#[CoversClass(TargetedCatalogPromotionProcessor::class)]
final class TargetedCatalogPromotionProcessorTest extends TestCase
{
    public function testImplementsTargetedCatalogPromotionProcessorInterface(): void
    {
        $this->assertInstanceOf(TargetedCatalogPromotionProcessorInterface::class, $this->createProcessor());
    }

    public function testDispatchesTheUnionOfAppliedAndInScopeVariantsWithoutDuplicates(): void
    {
        $taxonsScope = $this->createScope('for_taxons');
        $productsScope = $this->createScope('for_products');
        $catalogPromotion = $this->createCatalogPromotion($taxonsScope, $productsScope);

        $appliedPromotionProvider = $this->createMock(VariantCodesWithAppliedPromotionProviderInterface::class);
        $appliedPromotionProvider->expects($this->once())->method('provide')->with($catalogPromotion)->willReturn(['A', 'B']);

        $taxonsProvider = $this->createMock(VariantCodesInScopeProviderInterface::class);
        $taxonsProvider->expects($this->once())->method('provide')->with($taxonsScope)->willReturn(['B', 'C']);

        $productsProvider = $this->createMock(VariantCodesInScopeProviderInterface::class);
        $productsProvider->expects($this->once())->method('provide')->with($productsScope)->willReturn(['C', 'D']);

        $commandDispatcher = $this->createMock(ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface::class);
        $commandDispatcher->expects($this->once())->method('updateVariants')->with(['A', 'B', 'C', 'D']);

        $allProductVariantsProcessor = $this->createMock(AllProductVariantsCatalogPromotionsProcessorInterface::class);
        $allProductVariantsProcessor->expects($this->never())->method('process');

        $this->createProcessor(
            appliedPromotionProvider: $appliedPromotionProvider,
            commandDispatcher: $commandDispatcher,
            allProductVariantsProcessor: $allProductVariantsProcessor,
            taxonsProvider: $taxonsProvider,
            productsProvider: $productsProvider,
        )->process($catalogPromotion);
    }

    public function testDispatchesVariantsWhereThePromotionIsAppliedEvenIfTheyAreNoLongerInScope(): void
    {
        $taxonsScope = $this->createScope('for_taxons');
        $catalogPromotion = $this->createCatalogPromotion($taxonsScope);

        $appliedPromotionProvider = $this->createStub(VariantCodesWithAppliedPromotionProviderInterface::class);
        $appliedPromotionProvider->method('provide')->willReturn(['NO_LONGER_IN_SCOPE']);

        $taxonsProvider = $this->createStub(VariantCodesInScopeProviderInterface::class);
        $taxonsProvider->method('provide')->willReturn([]);

        $commandDispatcher = $this->createMock(ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface::class);
        $commandDispatcher->expects($this->once())->method('updateVariants')->with(['NO_LONGER_IN_SCOPE']);

        $this->createProcessor(
            appliedPromotionProvider: $appliedPromotionProvider,
            commandDispatcher: $commandDispatcher,
            taxonsProvider: $taxonsProvider,
        )->process($catalogPromotion);
    }

    public function testDispatchesOnlyAppliedVariantsIfThePromotionHasNoScope(): void
    {
        $catalogPromotion = $this->createCatalogPromotion();

        $appliedPromotionProvider = $this->createStub(VariantCodesWithAppliedPromotionProviderInterface::class);
        $appliedPromotionProvider->method('provide')->willReturn(['A']);

        $commandDispatcher = $this->createMock(ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface::class);
        $commandDispatcher->expects($this->once())->method('updateVariants')->with(['A']);

        $allProductVariantsProcessor = $this->createMock(AllProductVariantsCatalogPromotionsProcessorInterface::class);
        $allProductVariantsProcessor->expects($this->never())->method('process');

        $this->createProcessor(
            appliedPromotionProvider: $appliedPromotionProvider,
            commandDispatcher: $commandDispatcher,
            allProductVariantsProcessor: $allProductVariantsProcessor,
        )->process($catalogPromotion);
    }

    public function testProcessesAllVariantsIfAScopeHasNoProvider(): void
    {
        $catalogPromotion = $this->createCatalogPromotion(
            $this->createScope('for_taxons'),
            $this->createScope('custom_scope'),
        );

        $appliedPromotionProvider = $this->createMock(VariantCodesWithAppliedPromotionProviderInterface::class);
        $appliedPromotionProvider->expects($this->never())->method('provide');

        $taxonsProvider = $this->createMock(VariantCodesInScopeProviderInterface::class);
        $taxonsProvider->expects($this->never())->method('provide');

        $commandDispatcher = $this->createMock(ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface::class);
        $commandDispatcher->expects($this->never())->method('updateVariants');

        $allProductVariantsProcessor = $this->createMock(AllProductVariantsCatalogPromotionsProcessorInterface::class);
        $allProductVariantsProcessor->expects($this->once())->method('process');

        $this->createProcessor(
            appliedPromotionProvider: $appliedPromotionProvider,
            commandDispatcher: $commandDispatcher,
            allProductVariantsProcessor: $allProductVariantsProcessor,
            taxonsProvider: $taxonsProvider,
        )->process($catalogPromotion);
    }

    public function testDispatchesNothingIfNoVariantIsConcerned(): void
    {
        $catalogPromotion = $this->createCatalogPromotion($this->createScope('for_taxons'));

        $appliedPromotionProvider = $this->createStub(VariantCodesWithAppliedPromotionProviderInterface::class);
        $appliedPromotionProvider->method('provide')->willReturn([]);

        $taxonsProvider = $this->createStub(VariantCodesInScopeProviderInterface::class);
        $taxonsProvider->method('provide')->willReturn([]);

        $commandDispatcher = $this->createMock(ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface::class);
        $commandDispatcher->expects($this->never())->method('updateVariants');

        $allProductVariantsProcessor = $this->createMock(AllProductVariantsCatalogPromotionsProcessorInterface::class);
        $allProductVariantsProcessor->expects($this->never())->method('process');

        $this->createProcessor(
            appliedPromotionProvider: $appliedPromotionProvider,
            commandDispatcher: $commandDispatcher,
            allProductVariantsProcessor: $allProductVariantsProcessor,
            taxonsProvider: $taxonsProvider,
        )->process($catalogPromotion);
    }

    private function createProcessor(
        ?VariantCodesWithAppliedPromotionProviderInterface $appliedPromotionProvider = null,
        ?ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface $commandDispatcher = null,
        ?AllProductVariantsCatalogPromotionsProcessorInterface $allProductVariantsProcessor = null,
        ?VariantCodesInScopeProviderInterface $taxonsProvider = null,
        ?VariantCodesInScopeProviderInterface $productsProvider = null,
    ): TargetedCatalogPromotionProcessor {
        $taxonsProvider ??= $this->createStub(VariantCodesInScopeProviderInterface::class);
        $productsProvider ??= $this->createStub(VariantCodesInScopeProviderInterface::class);

        return new TargetedCatalogPromotionProcessor(
            new ServiceLocator([
                'for_taxons' => static fn (): VariantCodesInScopeProviderInterface => $taxonsProvider,
                'for_products' => static fn (): VariantCodesInScopeProviderInterface => $productsProvider,
            ]),
            $appliedPromotionProvider ?? $this->createStub(VariantCodesWithAppliedPromotionProviderInterface::class),
            $commandDispatcher ?? $this->createStub(ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface::class),
            $allProductVariantsProcessor ?? $this->createStub(AllProductVariantsCatalogPromotionsProcessorInterface::class),
        );
    }

    private function createScope(string $type): CatalogPromotionScopeInterface
    {
        $scope = $this->createStub(CatalogPromotionScopeInterface::class);
        $scope->method('getType')->willReturn($type);

        return $scope;
    }

    private function createCatalogPromotion(CatalogPromotionScopeInterface ...$scopes): CatalogPromotionInterface
    {
        $catalogPromotion = $this->createStub(CatalogPromotionInterface::class);
        $catalogPromotion->method('getScopes')->willReturn(new ArrayCollection($scopes));

        return $catalogPromotion;
    }
}
