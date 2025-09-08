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

namespace Tests\Sylius\Bundle\CoreBundle\Order\NumberGenerator;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Order\NumberGenerator\SequentialOrderNumberGenerator;
use Sylius\Bundle\OrderBundle\NumberGenerator\OrderNumberGeneratorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderSequenceInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class SequentialOrderNumberGeneratorTest extends TestCase
{
    private MockObject&RepositoryInterface $sequenceRepository;

    private FactoryInterface&MockObject $sequenceFactory;

    private EntityManagerInterface&MockObject $sequenceManager;

    private SequentialOrderNumberGenerator $sequentialOrderNumberGenerator;

    protected function setUp(): void
    {
        $this->sequenceRepository = $this->createMock(RepositoryInterface::class);
        $this->sequenceFactory = $this->createMock(FactoryInterface::class);
        $this->sequenceManager = $this->createMock(EntityManagerInterface::class);
        $this->sequentialOrderNumberGenerator = new SequentialOrderNumberGenerator(
            $this->sequenceRepository,
            $this->sequenceFactory,
            $this->sequenceManager,
        );
    }

    public function testImplementsAnOrderNumberGeneratorInterface(): void
    {
        $this->assertInstanceOf(OrderNumberGeneratorInterface::class, $this->sequentialOrderNumberGenerator);
    }

    public function testGeneratesAnOrderNumber(): void
    {
        $sequence = $this->createMock(OrderSequenceInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $sequence->expects($this->once())->method('getIndex')->willReturn(6);
        $sequence->expects($this->once())->method('getVersion')->willReturn(7);
        $this->sequenceRepository->expects($this->once())->method('findOneBy')->with([])->willReturn($sequence);
        $this->sequenceManager->expects($this->once())->method('lock')->with($sequence, LockMode::OPTIMISTIC, 7);
        $sequence->expects($this->once())->method('incrementIndex');

        $this->assertSame('000000007', $this->sequentialOrderNumberGenerator->generate($order));
    }

    public function testGeneratesAnOrderNumberWhenSequenceIsNull(): void
    {
        $sequence = $this->createMock(OrderSequenceInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $sequence->expects($this->once())->method('getIndex')->willReturn(0);
        $sequence->expects($this->once())->method('getVersion')->willReturn(1);
        $this->sequenceRepository->expects($this->once())->method('findOneBy')->with([])->willReturn(null);
        $this->sequenceFactory->expects($this->once())->method('createNew')->willReturn($sequence);
        $this->sequenceManager->expects($this->once())->method('persist')->with($sequence);
        $this->sequenceManager->expects($this->once())->method('lock')->with($sequence, LockMode::OPTIMISTIC, 1);
        $sequence->expects($this->once())->method('incrementIndex');

        $this->assertSame('000000001', $this->sequentialOrderNumberGenerator->generate($order));
    }
}
