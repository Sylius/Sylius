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

namespace Tests\Sylius\Component\Attribute\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Attribute\Model\AttributeTranslation;
use Sylius\Component\Attribute\Model\AttributeTranslationInterface;

class AttributeTranslationTest extends TestCase
{
    private AttributeTranslation $translation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->translation = new AttributeTranslation();
    }

    public function testCanBeInstantiated(): void
    {
        self::assertInstanceOf(AttributeTranslation::class, $this->translation);
    }

    public function testShouldImplementAttributeTranslationInterface(): void
    {
        self::assertInstanceOf(AttributeTranslationInterface::class, $this->translation);
    }

    public function testShouldHasNoIdByDefault(): void
    {
        self::assertNull($this->translation->getId());
    }

    public function testShouldHasNoNameByDefault(): void
    {
        self::assertNull($this->translation->getName());
    }

    public function testNameShouldBeMutable(): void
    {
        $this->translation->setName('Size');
        self::assertSame('Size', $this->translation->getName());
    }
}
