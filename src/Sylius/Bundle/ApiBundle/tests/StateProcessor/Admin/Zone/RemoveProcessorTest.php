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

namespace Tests\Sylius\Bundle\ApiBundle\StateProcessor\Admin\Zone;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\State\ProcessorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\Zone\RemoveProcessor;
use Sylius\Component\Addressing\Checker\ZoneDeletionCheckerInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Exception\ResourceDeleteException;

final class RemoveProcessorTest extends TestCase
{
    private MockObject&ProcessorInterface $processor;

    private MockObject&ZoneDeletionCheckerInterface $zoneDeletionChecker;

    private RemoveProcessor $removeProcessor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = $this->createMock(ProcessorInterface::class);
        $this->zoneDeletionChecker = $this->createMock(ZoneDeletionCheckerInterface::class);
        $this->removeProcessor = new RemoveProcessor($this->processor, $this->zoneDeletionChecker);
    }

    public function testThrowsAnExceptionIfObjectIsNotAZone(): void
    {
        $deleteOperation = new Delete();

        $this->zoneDeletionChecker->expects(self::never())->method('isDeletable');

        $this->processor->expects(self::never())->method('process')->with($this->any());

        self::expectException(\InvalidArgumentException::class);

        $this->removeProcessor->process(new \stdClass(), $deleteOperation, [], []);
    }

    public function testThrowsExceptionIfZoneIsNotDeletable(): void
    {
        $deleteOperation = new Delete();
        /** @var ZoneInterface|MockObject $zoneMock */
        $zoneMock = $this->createMock(ZoneInterface::class);

        $this->zoneDeletionChecker->expects(self::once())
            ->method('isDeletable')
            ->with($zoneMock)
            ->willReturn(false);

        $this->processor->expects(self::never())->method('process')->with($this->any());

        self::expectException(ResourceDeleteException::class);

        $this->removeProcessor->process($zoneMock, $deleteOperation, [], []);
    }

    public function testUsesDecoratedDataPersisterToRemoveChannel(): void
    {
        $deleteOperation = new Delete();
        /** @var ZoneInterface|MockObject $zoneMock */
        $zoneMock = $this->createMock(ZoneInterface::class);

        $this->zoneDeletionChecker->expects(self::once())->method('isDeletable')->with($zoneMock)->willReturn(true);

        $this->processor->expects(self::once())
            ->method('process')
            ->with($zoneMock, $deleteOperation, [], [])
            ->willReturn($zoneMock);

        $this->removeProcessor->process($zoneMock, $deleteOperation);
    }
}
