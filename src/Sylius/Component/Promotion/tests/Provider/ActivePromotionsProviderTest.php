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

namespace Tests\Sylius\Component\Promotion\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Provider\ActivePromotionsProvider;
use Sylius\Component\Promotion\Provider\PreQualifiedPromotionsProviderInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;

final class ActivePromotionsProviderTest extends TestCase
{
    /** @var PromotionRepositoryInterface<PromotionInterface>&MockObject */
    private MockObject&PromotionRepositoryInterface $promotionRepository;

    private ActivePromotionsProvider $provider;

    protected function setUp(): void
    {
        $this->promotionRepository = $this->createMock(PromotionRepositoryInterface::class);
        $this->provider = new ActivePromotionsProvider($this->promotionRepository);
    }

    public function testShouldImplementActivePromotionsProviderInterface(): void
    {
        $this->assertInstanceOf(PreQualifiedPromotionsProviderInterface:: class, $this->provider);
    }

    public function testShouldProvideActivePromotions(): void
    {
        $promotionSubject = $this->createMock(PromotionSubjectInterface::class);
        $promotion1 = $this->createMock(PromotionInterface::class);
        $promotion2 = $this->createMock(PromotionInterface::class);
        $this->promotionRepository->expects($this->once())->method('findActive')->willReturn([$promotion1, $promotion2]);

        $this->assertSame([$promotion1, $promotion2], $this->provider->getPromotions($promotionSubject));
    }
}
