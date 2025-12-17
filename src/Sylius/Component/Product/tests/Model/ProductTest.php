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

namespace Tests\Sylius\Component\Product\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Product\Model\Product;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Resource\Model\ToggleableInterface;

final class ProductTest extends TestCase
{
    private MockObject&ProductAttributeValueInterface $productAttribute;

    private Product $product;

    private AttributeInterface&MockObject $attribute;

    private MockObject&ProductAttributeValueInterface $attributeValueEN;

    private MockObject&ProductAttributeValueInterface $attributeValuePL;

    private MockObject&ProductAttributeValueInterface $attributeValueFR;

    private MockObject&ProductVariantInterface $firstVariant;

    private MockObject&ProductVariantInterface $secondVariant;

    private MockObject&ProductOptionInterface $option;

    private MockObject&ProductAssociationInterface $association;

    protected function setUp(): void
    {
        $this->productAttribute = $this->createMock(ProductAttributeValueInterface::class);
        $this->product = new Product();
        $this->product->setCurrentLocale('en_US');
        $this->product->setFallbackLocale('en_US');
        $this->attribute = $this->createMock(AttributeInterface::class);
        $this->attributeValueEN = $this->createMock(ProductAttributeValueInterface::class);
        $this->attributeValuePL = $this->createMock(ProductAttributeValueInterface::class);
        $this->attributeValueFR = $this->createMock(ProductAttributeValueInterface::class);
        $this->firstVariant = $this->createMock(ProductVariantInterface::class);
        $this->secondVariant = $this->createMock(ProductVariantInterface::class);
        $this->option = $this->createMock(ProductOptionInterface::class);
        $this->association = $this->createMock(ProductAssociationInterface::class);
    }

    public function testImplementsProductInterface(): void
    {
        self::assertInstanceOf(ProductInterface::class, $this->product);
    }

    public function testImplementsToggleableInterface(): void
    {
        self::assertInstanceOf(ToggleableInterface::class, $this->product);
    }

    public function testHasNoIdByDefault(): void
    {
        $this->assertNull($this->product->getId());
    }

    public function testHasNoNameByDefault(): void
    {
        $this->assertNull($this->product->getName());
    }

    public function testItsNameIsMutable(): void
    {
        $this->product->setName('Super product');
        $this->assertSame('Super product', $this->product->getName());
    }

    public function testHasADescriptor(): void
    {
        $this->product->setName('Name');
        $this->product->setCode('code');

        $this->assertSame('Name (code)', $this->product->getDescriptor());
    }

    public function testHasNoSlugByDefault(): void
    {
        $this->assertNull($this->product->getSlug());
    }

    public function testItsSlugIsMutable(): void
    {
        $this->product->setSlug('super-product');
        $this->assertSame('super-product', $this->product->getSlug());
    }

    public function testHasNoDescriptionByDefault(): void
    {
        $this->assertNull($this->product->getDescription());
    }

    public function testItsDescriptionIsMutable(): void
    {
        $this->product->setDescription('This product is super cool because...');
        $this->assertSame('This product is super cool because...', $this->product->getDescription());
    }

    public function testAddsAttribute(): void
    {
        $this->productAttribute
            ->expects($this->once())
            ->method('setProduct')
            ->with($this->product)
        ;

        $this->product->addAttribute($this->productAttribute);
        $this->assertTrue($this->product->hasAttribute($this->productAttribute));
    }

    public function testRemovesAttribute(): void
    {
        $this->productAttribute
            ->expects($this->exactly(2))
            ->method('setProduct')
            ->willReturnCallback(function ($arg) {
                static $callIndex = 0;
                $expectedArgs = [$this->product, null];

                TestCase::assertSame($expectedArgs[$callIndex], $arg);
                ++$callIndex;
            })
        ;

        $this->product->addAttribute($this->productAttribute);
        $this->assertTrue($this->product->hasAttribute($this->productAttribute));

        $this->product->removeAttribute($this->productAttribute);
        $this->assertFalse($this->product->hasAttribute($this->productAttribute));
    }

    public function testRefusesToAddNonProductAttribute(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $foreignAttribute = $this->createMock(AttributeValueInterface::class);
        $this->product->addAttribute($foreignAttribute);
    }

    public function testRefusesToRemoveNonProductAttribute(): void
    {
        $attributeValue = $this->createMock(AttributeValueInterface::class);

        $this->expectException(\InvalidArgumentException::class);

        $this->product->removeAttribute($attributeValue);
    }

    public function testReturnsAttributesByALocaleWithoutABaseLocale(): void
    {
        $this->attributeValueEN->expects($this->once())->method('setProduct')->with($this->product);
        $this->attributeValueEN->expects($this->atLeastOnce())->method('getLocaleCode')->willReturn('en_US');
        $this->attributeValueEN->expects($this->atLeastOnce())->method('getAttribute')->willReturn($this->attribute);
        $this->attributeValueEN->expects($this->once())->method('getCode')->willReturn('colour');

        $this->attributeValuePL->expects($this->once())->method('setProduct')->with($this->product);
        $this->attributeValuePL->expects($this->atLeastOnce())->method('getLocaleCode')->willReturn('pl_PL');
        $this->attributeValuePL->expects($this->atLeastOnce())->method('getAttribute')->willReturn($this->attribute);
        $this->attributeValuePL->expects($this->once())->method('getCode')->willReturn('colour');

        $this->product->addAttribute($this->attributeValueEN);
        $this->product->addAttribute($this->attributeValuePL);

        $resultEN = $this->product->getAttributesByLocale('en_US', 'pl_PL');
        $resultPL = $this->product->getAttributesByLocale('pl_PL', 'en_US');

        $this->assertEquals([$this->attributeValueEN], iterator_to_array($resultEN));
        $this->assertEquals([$this->attributeValuePL], iterator_to_array($resultPL));
    }

    public function testReturnsAttributesByALocaleWithABaseLocale(): void
    {
        $this->attributeValueEN->expects($this->once())->method('setProduct')->with($this->product);
        $this->attributeValueEN->expects($this->atLeastOnce())->method('getLocaleCode')->willReturn('en_US');
        $this->attributeValueEN->expects($this->atLeastOnce())->method('getAttribute')->willReturn($this->attribute);
        $this->attributeValueEN->expects($this->once())->method('getCode')->willReturn('colour');

        $this->attributeValuePL->expects($this->once())->method('setProduct')->with($this->product);
        $this->attributeValuePL->expects($this->atLeastOnce())->method('getLocaleCode')->willReturn('pl_PL');
        $this->attributeValuePL->expects($this->atLeastOnce())->method('getAttribute')->willReturn($this->attribute);
        $this->attributeValuePL->expects($this->atLeastOnce())->method('getCode')->willReturn('colour');

        $this->attributeValueFR->expects($this->once())->method('setProduct')->with($this->product);
        $this->attributeValueFR->expects($this->atLeastOnce())->method('getLocaleCode')->willReturn('fr_FR');
        $this->attributeValueFR->expects($this->atLeastOnce())->method('getAttribute')->willReturn($this->attribute);
        $this->attributeValueFR->expects($this->once())->method('getCode')->willReturn('colour');

        $this->product->addAttribute($this->attributeValueEN);
        $this->product->addAttribute($this->attributeValuePL);
        $this->product->addAttribute($this->attributeValueFR);

        $resultEN = $this->product->getAttributesByLocale('en_US', 'pl_PL');
        $resultPL = $this->product->getAttributesByLocale('pl_PL', 'fr_FR');
        $resultFR = $this->product->getAttributesByLocale('fr_FR', 'en_US');

        $this->assertEquals([$this->attributeValueEN], iterator_to_array($resultEN));
        $this->assertEquals([$this->attributeValuePL], iterator_to_array($resultPL));
        $this->assertEquals([$this->attributeValueFR], iterator_to_array($resultFR));
    }

    public function testReturnsAttributesByAFallbackLocaleWhenThereIsNoValueForAGivenLocale(): void
    {
        $this->attributeValueEN->expects($this->once())->method('setProduct')->with($this->product);
        $this->attributeValueEN->expects($this->atLeastOnce())->method('getLocaleCode')->willReturn('en_US');
        $this->attributeValueEN->expects($this->once())->method('getAttribute')->willReturn($this->attribute);
        $this->attributeValueEN->expects($this->once())->method('getCode')->willReturn('colour');

        $this->product->addAttribute($this->attributeValueEN);

        $resultEN = $this->product->getAttributesByLocale('pl_PL', 'en_US');

        $this->assertEquals([$this->attributeValueEN], iterator_to_array($resultEN));
    }

    public function testReturnsAttributesByAFallbackLocaleWhenThereIsAnEmptyValueForAGivenLocale(): void
    {
        $this->attributeValueEN->expects($this->once())->method('setProduct')->with($this->product);
        $this->attributeValueEN->expects($this->atLeastOnce())->method('getLocaleCode')->willReturn('en_US');
        $this->attributeValueEN->expects($this->atLeastOnce())->method('getAttribute')->willReturn($this->attribute);
        $this->attributeValueEN->expects($this->once())->method('getCode')->willReturn('colour');

        $this->attributeValuePL->expects($this->once())->method('setProduct')->with($this->product);
        $this->attributeValuePL->expects($this->atLeastOnce())->method('getLocaleCode')->willReturn('pl_PL');
        $this->attributeValuePL->expects($this->atLeastOnce())->method('getAttribute')->willReturn($this->attribute);
        $this->attributeValuePL->expects($this->once())->method('getCode')->willReturn('colour');

        $this->product->addAttribute($this->attributeValueEN);
        $this->product->addAttribute($this->attributeValuePL);

        $resultEN = $this->product->getAttributesByLocale('en_US', 'pl_PL');
        $resultPL = $this->product->getAttributesByLocale('pl_PL', 'en_US');

        $this->assertEquals([$this->attributeValueEN], iterator_to_array($resultEN));
        $this->assertEquals([$this->attributeValuePL], iterator_to_array($resultPL));
    }

    public function testReturnsAttributesByABaseLocaleWhenThereIsNoValueForAGivenLocaleOrAFallbackLocale(): void
    {
        $this->attributeValueFR->expects($this->once())->method('setProduct')->with($this->product);
        $this->attributeValueFR->expects($this->atLeastOnce())->method('getLocaleCode')->willReturn('fr_FR');
        $this->attributeValueFR->expects($this->atLeastOnce())->method('getAttribute')->willReturn($this->attribute);
        $this->attributeValueFR->expects($this->atLeastOnce())->method('getCode')->willReturn('colour');

        $this->product->addAttribute($this->attributeValueFR);

        $resultFR = $this->product->getAttributesByLocale('pl_PL', 'en_US', 'fr_FR');

        $this->assertEquals([$this->attributeValueFR], iterator_to_array($resultFR));
    }

    public function testReturnsAttributesByABaseLocaleWhenThereIsAnEmptyValueForAGivenLocaleOrAFallbackLocale(): void
    {
        $this->attributeValueEN->expects($this->once())->method('setProduct')->with($this->product);
        $this->attributeValueEN->expects($this->atLeastOnce())->method('getLocaleCode')->willReturn('en_US');
        $this->attributeValueEN->expects($this->atLeastOnce())->method('getAttribute')->willReturn($this->attribute);
        $this->attributeValueEN->expects($this->once())->method('getCode')->willReturn('colour');

        $this->attributeValuePL->expects($this->once())->method('setProduct')->with($this->product);
        $this->attributeValuePL->expects($this->atLeastOnce())->method('getLocaleCode')->willReturn('pl_PL');
        $this->attributeValuePL->expects($this->atLeastOnce())->method('getAttribute')->willReturn($this->attribute);
        $this->attributeValuePL->expects($this->once())->method('getCode')->willReturn('colour');

        $this->attributeValueFR->expects($this->once())->method('setProduct')->with($this->product);
        $this->attributeValueFR->expects($this->atLeastOnce())->method('getLocaleCode')->willReturn('fr_FR');
        $this->attributeValueFR->expects($this->atLeastOnce())->method('getAttribute')->willReturn($this->attribute);
        $this->attributeValueFR->expects($this->once())->method('getCode')->willReturn('colour');

        $this->product->addAttribute($this->attributeValueEN);
        $this->product->addAttribute($this->attributeValuePL);
        $this->product->addAttribute($this->attributeValueFR);

        $resultEN = $this->product->getAttributesByLocale('en_US', 'pl_PL');
        $resultPL = $this->product->getAttributesByLocale('pl_PL', 'fr_FR');
        $resultFR = $this->product->getAttributesByLocale('fr_FR', 'en_US');

        $this->assertEquals([$this->attributeValueEN], iterator_to_array($resultEN));
        $this->assertEquals([$this->attributeValuePL], iterator_to_array($resultPL));
        $this->assertEquals([$this->attributeValueFR], iterator_to_array($resultFR));
    }

    public function testHasNoVariantsByDefault(): void
    {
        self::assertFalse($this->product->hasVariants());
    }

    public function testItsSaysItHasVariantsOnlyIfMultipleVariantsAreDefined(): void
    {
        $this->firstVariant->expects($this->once())->method('setProduct')->with($this->product);

        $this->secondVariant->expects($this->once())->method('setProduct')->with($this->product);

        $this->product->addVariant($this->firstVariant);
        $this->product->addVariant($this->secondVariant);
        $this->assertTrue($this->product->hasVariants());
    }

    public function testDoesNotIncludeUnavailableVariantsInAvailableVariants(): void
    {
        $this->firstVariant->expects($this->once())->method('setProduct')->with($this->product);

        $this->product->addVariant($this->firstVariant);
    }

    public function testReturnsAvailableVariants(): void
    {
        $unavailableVariant = $this->createMock(ProductVariantInterface::class);
        $unavailableVariant->expects($this->once())->method('setProduct')->with($this->product);

        $this->firstVariant->expects($this->once())->method('setProduct')->with($this->product);

        $this->product->addVariant($unavailableVariant);
        $this->product->addVariant($this->firstVariant);
    }

    public function testHasNoOptionsByDefault(): void
    {
        $this->assertFalse($this->product->hasOptions());
    }

    public function testItsSaysItHasOptionsOnlyIfAnyOptionDefined(): void
    {
        $this->product->addOption($this->option);
        $this->assertTrue($this->product->hasOptions());
    }

    public function testAddsOptionProperly(): void
    {
        $this->product->addOption($this->option);
        $this->assertTrue($this->product->hasOption($this->option));
    }

    public function testRemovesOptionProperly(): void
    {
        $this->product->addOption($this->option);
        $this->assertTrue($this->product->hasOption($this->option));

        $this->product->removeOption($this->option);
        $this->assertFalse($this->product->hasOption($this->option));
    }

    public function testItsCreationDateIsMutable(): void
    {
        $creationDate = new \DateTimeImmutable();
        $this->product->setCreatedAt($creationDate);
        $this->assertSame($creationDate, $this->product->getCreatedAt());
    }

    public function testHasNoLastUpdateDateByDefault(): void
    {
        $this->assertNull($this->product->getUpdatedAt());
    }

    public function testItsLastUpdateDateIsMutable(): void
    {
        $updateDate = new \DateTimeImmutable();
        $this->product->setUpdatedAt($updateDate);
        $this->assertSame($updateDate, $this->product->getUpdatedAt());
    }

    public function testEnabledByDefault(): void
    {
        $this->assertTrue($this->product->isEnabled());
    }

    public function testToggleable(): void
    {
        $this->product->disable();
        self::assertFalse($this->product->isEnabled());

        $this->product->enable();
        $this->assertTrue($this->product->isEnabled());
    }

    public function testAddsAssociation(): void
    {
        $this->association->expects($this->once())->method('setOwner')->with($this->product);
        $this->product->addAssociation($this->association);

        $this->assertTrue($this->product->hasAssociation($this->association));
    }

    public function testAllowsToRemoveAssociation(): void
    {
        $this->association
            ->expects($this->exactly(2))
            ->method('setOwner')
            ->willReturnCallback(function ($arg) {
                static $callIndex = 0;
                $expectedArgs = [$this->product, null];

                TestCase::assertSame($expectedArgs[$callIndex], $arg);
                ++$callIndex;
            })
        ;

        $this->product->addAssociation($this->association);
        $this->product->removeAssociation($this->association);

        $this->assertFalse($this->product->hasAssociation($this->association));
    }

    public function testSimpleIfItHasOneVariantAndNoOptions(): void
    {
        $this->firstVariant->expects($this->once())->method('setProduct')->with($this->product);
        $this->product->addVariant($this->firstVariant);

        $this->assertTrue($this->product->isSimple());
        $this->assertFalse($this->product->isConfigurable());
    }

    public function testConfigurableIfItHasAtLeastTwoVariants(): void
    {
        $this->firstVariant->expects($this->once())->method('setProduct')->with($this->product);
        $this->product->addVariant($this->firstVariant);

        $this->secondVariant->expects($this->once())->method('setProduct')->with($this->product);
        $this->product->addVariant($this->secondVariant);

        $this->assertTrue($this->product->isConfigurable());
        $this->assertFalse($this->product->isSimple());
    }

    public function testConfigurableIfItHasOneVariantAndAtLeastOneOption(): void
    {
        $this->firstVariant->expects($this->once())->method('setProduct')->with($this->product);
        $this->product->addVariant($this->firstVariant);
        $this->product->addOption($this->option);

        $this->assertTrue($this->product->isConfigurable());
        $this->assertFalse($this->product->isSimple());
    }
}
