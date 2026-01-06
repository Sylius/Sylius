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

namespace Tests\Sylius\Bundle\ApiBundle\StateProvider\Shop\Channel;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\Channel\CollectionProvider;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelInterface;

final class CollectionProviderTest extends TestCase
{
    private MockObject&SectionProviderInterface $sectionProvider;

    private CollectionProvider $collectionProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->collectionProvider = new CollectionProvider($this->sectionProvider);
    }

    public function testProvidesChannel(): void
    {
        $channelMock = $this->createMock(ChannelInterface::class);

        $operation = new GetCollection(class: Channel::class);

        $this->sectionProvider
            ->expects(self::once())
            ->method('getSection')
            ->willReturn(new ShopApiSection());

        $result = $this->collectionProvider->provide(
            $operation,
            [],
            ['sylius_api_channel' => $channelMock],
        );

        $this->assertEquals([$channelMock], $result);
    }

    public function testThrowsAnExceptionWhenOperationClassIsNotChannel(): void
    {
        $operationMock = $this->createMock(Operation::class);

        $operationMock
            ->expects(self::once())
            ->method('getClass')
            ->willReturn(\stdClass::class);

        self::expectException(\InvalidArgumentException::class);

        $this->collectionProvider->provide($operationMock);
    }

    public function testThrowsAnExceptionWhenOperationIsNotGetCollection(): void
    {
        $operationMock = $this->createMock(Operation::class);

        self::expectException(\InvalidArgumentException::class);

        $operationMock
            ->expects(self::once())
            ->method('getClass')
            ->willReturn(Channel::class);

        $this->collectionProvider->provide($operationMock);
    }

    public function testThrowsAnExceptionWhenOperationIsNotInShopApiSection(): void
    {
        $channelMock = $this->createMock(ChannelInterface::class);

        $operation = new GetCollection(class: Channel::class);

        $this->sectionProvider
            ->expects(self::once())
            ->method('getSection')
            ->willReturn(new AdminApiSection());

        self::expectException(\InvalidArgumentException::class);
        $this->collectionProvider->provide(
            $operation,
            [],
            ['sylius_api_channel' => $channelMock],
        );
    }

    public function testThrowsAnExceptionWhenContextDoesNotHaveChannel(): void
    {
        $operation = new GetCollection(class: Channel::class);

        $this->sectionProvider
            ->expects(self::once())
            ->method('getSection')
            ->willReturn(new ShopApiSection());

        self::expectException(\InvalidArgumentException::class);

        $this->collectionProvider->provide($operation, [], []);
    }
}
