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

namespace Tests\Sylius\Bundle\OrderBundle\NumberGenerator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\OrderBundle\NumberGenerator\OrderNumberGeneratorInterface;
use Sylius\Bundle\OrderBundle\NumberGenerator\SequentialOrderNumberGenerator;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderSequenceInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;  // Update the import
use Sylius\Resource\Factory\FactoryInterface;

final class SequentialOrderNumberGeneratorTest extends TestCase
{
    /** @var RepositoryInterface<OrderSequenceInterface>&MockObject */
    private RepositoryInterface $sequenceRepository;

    /** @var FactoryInterface&MockObject */
    private FactoryInterface $sequenceFactory;

    private SequentialOrderNumberGenerator $sequentialOrderNumberGenerator;

    /** @var OrderSequenceInterface&MockObject */
    private OrderSequenceInterface $sequence;

    /** @var OrderInterface&MockObject */
    private OrderInterface $order;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sequenceRepository = $this->createMock(RepositoryInterface::class);
        $this->sequenceFactory = $this->createMock(FactoryInterface::class);
        $this->sequentialOrderNumberGenerator = new SequentialOrderNumberGenerator(
            $this->sequenceRepository,
            $this->sequenceFactory,
        );
        $this->sequence = $this->createMock(OrderSequenceInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
    }

    public function testImplementsAnOrderNumberGeneratorInterface(): void
    {
        self::assertInstanceOf(OrderNumberGeneratorInterface::class, $this->sequentialOrderNumberGenerator);
    }

    public function testGeneratesAnOrderNumber(): void
    {
        $this->sequence->expects(self::once())->method('getIndex')->willReturn(6);

        $this->sequenceRepository->expects(self::once())
            ->method('findOneBy')
            ->with([])
            ->willReturn($this->sequence);

        $this->sequence->expects(self::once())->method('incrementIndex');

        self::assertSame('000000007', $this->sequentialOrderNumberGenerator->generate($this->order));
    }

    public function testGeneratesAnOrderNumberWhenSequenceIsNull(): void
    {
        $this->sequence->expects(self::once())->method('getIndex')->willReturn(0);

        $this->sequenceRepository->expects(self::once())
            ->method('findOneBy')
            ->with([])
            ->willReturn(null);

        $this->sequenceFactory->expects(self::once())->method('createNew')->willReturn($this->sequence);

        $this->sequenceRepository->expects(self::once())->method('add')->with($this->sequence);

        $this->sequence->expects(self::once())->method('incrementIndex');

        self::assertSame('000000001', $this->sequentialOrderNumberGenerator->generate($this->order));
    }
}
