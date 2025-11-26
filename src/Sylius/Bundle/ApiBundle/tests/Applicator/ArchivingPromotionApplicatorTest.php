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

namespace Tests\Sylius\Bundle\ApiBundle\Applicator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use Sylius\Bundle\ApiBundle\Applicator\ArchivingPromotionApplicator;
use Sylius\Component\Core\Model\PromotionInterface;

final class ArchivingPromotionApplicatorTest extends TestCase
{
    private ClockInterface&MockObject $clock;

    private ArchivingPromotionApplicator $archivingPromotionApplicator;

    private MockObject&PromotionInterface $promotion;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clock = $this->createMock(ClockInterface::class);
        $this->archivingPromotionApplicator = new ArchivingPromotionApplicator($this->clock);
        $this->promotion = $this->createMock(PromotionInterface::class);
    }

    public function testArchivesPromotion(): void
    {
        $now = new \DateTimeImmutable();

        $this->clock->expects(self::once())->method('now')->willReturn($now);

        $this->promotion->expects(self::once())->method('setArchivedAt')->with($now);

        self::assertSame($this->promotion, $this->archivingPromotionApplicator->archive($this->promotion));
    }

    public function testRestoresPromotion(): void
    {
        $this->promotion->expects(self::once())
            ->method('setArchivedAt')
            ->with(null);

        self::assertSame($this->promotion, $this->archivingPromotionApplicator->restore($this->promotion));
    }
}
