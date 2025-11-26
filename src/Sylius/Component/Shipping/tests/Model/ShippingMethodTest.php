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

namespace Tests\Sylius\Component\Shipping\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethod;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Resource\Model\ToggleableInterface;

final class ShippingMethodTest extends TestCase
{
    private MockObject&ShippingCategoryInterface $shippingCategory;

    private ShippingMethod $shippingMethod;

    protected function setUp(): void
    {
        $this->shippingCategory = $this->createMock(ShippingCategoryInterface::class);
        $this->shippingMethod = new ShippingMethod();
        $this->shippingMethod->setCurrentLocale('pl_PL');
        $this->shippingMethod->setFallbackLocale('pl_PL');
    }

    public function testShouldImplementShippingMethodInterface(): void
    {
        $this->assertInstanceOf(ShippingMethodInterface::class, $this->shippingMethod);
    }

    public function testShouldImplementSyliusToggleableInterface(): void
    {
        $this->assertInstanceOf(ToggleableInterface::class, $this->shippingMethod);
    }

    public function testShouldNotHaveIdByDefault(): void
    {
        $this->assertNull($this->shippingMethod->getId());
    }

    public function testShouldCodeBeMutable(): void
    {
        $this->shippingMethod->setCode('SC2');

        $this->assertSame('SC2', $this->shippingMethod->getCode());
    }

    public function testShouldBeEnabledByDefault(): void
    {
        $this->assertTrue($this->shippingMethod->isEnabled());
    }

    public function testShouldAllowDisableItself(): void
    {
        $this->shippingMethod->setEnabled(false);

        $this->assertFalse($this->shippingMethod->isEnabled());
    }

    public function testShouldNotBelongToCategoryByDefault(): void
    {
        $this->assertNull($this->shippingMethod->getCategory());
    }

    public function testShouldAllowAssignItselfToCategory(): void
    {
        $this->shippingMethod->setCategory($this->shippingCategory);

        $this->assertSame($this->shippingCategory, $this->shippingMethod->getCategory());
    }

    public function testShouldAllowDetachItselfFromCategory(): void
    {
        $this->shippingMethod->setCategory($this->shippingCategory);

        $this->shippingMethod->setCategory(null);

        $this->assertNull($this->shippingMethod->getCategory());
    }

    public function testShouldMatchAnyCategoryRequirementByDefault(): void
    {
        $this->assertSame(
            ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY,
            $this->shippingMethod->getCategoryRequirement(),
        );
    }

    public function testShouldCategoryMatchingRequirementBeMutable(): void
    {
        $this->shippingMethod->setCategoryRequirement(
            ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_NONE,
        );

        $this->assertSame(
            ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_NONE,
            $this->shippingMethod->getCategoryRequirement(),
        );
    }

    public function testShouldBeUnnamedByDefault(): void
    {
        $this->assertNull($this->shippingMethod->getName());
    }

    public function testShouldNotHaveDescriptionByDefault(): void
    {
        $this->assertNull($this->shippingMethod->getDescription());
    }

    public function testShouldNameBeMutable(): void
    {
        $this->shippingMethod->setName('Shippable goods');

        $this->assertSame('Shippable goods', $this->shippingMethod->getName());
    }

    public function testShouldDescriptionBeMutable(): void
    {
        $this->shippingMethod->setDescription('Very good shipping, cheap price, good delivery time.');

        $this->assertSame('Very good shipping, cheap price, good delivery time.', $this->shippingMethod->getDescription());
    }

    public function testShouldReturnNameWhenConvertedToString(): void
    {
        $this->shippingMethod->setName('Shippable goods');

        $this->assertSame('Shippable goods', (string) $this->shippingMethod);
    }

    public function testShouldNotHaveCalculatorDefinedbyDefault(): void
    {
        $this->assertNull($this->shippingMethod->getCalculator());
    }

    public function testShouldCalculatorBeMutable(): void
    {
        $this->shippingMethod->setCalculator('default');

        $this->assertSame('default', $this->shippingMethod->getCalculator());
    }

    public function testShouldInitializeArrayForConfigurationByDefault(): void
    {
        $this->assertSame([], $this->shippingMethod->getConfiguration());
    }

    public function testShouldConfigurationBeMutable(): void
    {
        $this->shippingMethod->setConfiguration(['charge' => 5]);

        $this->assertSame(['charge' => 5], $this->shippingMethod->getConfiguration());
    }

    public function testShouldInitializeCreationDateByDefault(): void
    {
        $this->assertInstanceOf(\DateTimeInterface::class, $this->shippingMethod->getCreatedAt());
    }

    public function testShouldCreationDateBeMutable(): void
    {
        $date = new \DateTime();

        $this->shippingMethod->setCreatedAt($date);

        $this->assertSame($date, $this->shippingMethod->getCreatedAt());
    }

    public function testShouldNotHaveLastUpdateDateByDefault(): void
    {
        $this->assertNull($this->shippingMethod->getUpdatedAt());
    }

    public function testShouldLastUpdateDateBeMutable(): void
    {
        $date = new \DateTime();

        $this->shippingMethod->setUpdatedAt($date);

        $this->assertSame($date, $this->shippingMethod->getUpdatedAt());
    }

    public function testShouldNotHaveArchivingDateByDefault(): void
    {
        $this->assertNull($this->shippingMethod->getArchivedAt());
    }

    public function testShouldArchivingDateBeMutable(): void
    {
        $date = new \DateTime();

        $this->shippingMethod->setArchivedAt($date);

        $this->assertSame($date, $this->shippingMethod->getArchivedAt());
    }
}
