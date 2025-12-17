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

namespace Tests\Sylius\Bundle\ApiBundle\PropertyInfo\Extractor;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Sylius\Bundle\ApiBundle\PropertyInfo\Extractor\EmptyPropertyListExtractor;
use Symfony\Component\PropertyInfo\PropertyListExtractorInterface;

final class EmptyPropertyListExtractorTest extends TestCase
{
    private EmptyPropertyListExtractor $emptyPropertyListExtractor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->emptyPropertyListExtractor = new EmptyPropertyListExtractor();
    }

    public function testPropertyListExtractor(): void
    {
        self::assertInstanceOf(PropertyListExtractorInterface::class, $this->emptyPropertyListExtractor);
    }

    public function testProvidesEmptyListIfRequestedClassExists(): void
    {
        self::assertSame([], $this->emptyPropertyListExtractor->getProperties(PickupCart::class, []));
    }

    public function testProvidesNullIfRequestedClassDoesNotExist(): void
    {
        self::assertNull($this->emptyPropertyListExtractor->getProperties(\Serializable::class, []));
    }
}
