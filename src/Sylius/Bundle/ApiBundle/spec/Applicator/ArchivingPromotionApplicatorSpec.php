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

namespace spec\Sylius\Bundle\ApiBundle\Applicator;

use PhpSpec\ObjectBehavior;
use Psr\Clock\ClockInterface;
use Sylius\Component\Core\Model\PromotionInterface;

final class ArchivingPromotionApplicatorSpec extends ObjectBehavior
{
    function let(ClockInterface $clock)
    {
        $this->beConstructedWith($clock);
    }

    function it_archives_promotion(
        ClockInterface $clock,
        PromotionInterface $promotion,
    ): void {
        $now = new \DateTimeImmutable();
        $clock->now()->willReturn($now);

        $promotion->setArchivedAt($now)->shouldBeCalledOnce();

        $this->archive($promotion)->shouldReturn($promotion);
    }

    function it_restores_promotion(
        PromotionInterface $promotion,
    ): void {
        $promotion->setArchivedAt(null)->shouldBeCalledOnce();

        $this->restore($promotion)->shouldReturn($promotion);
    }
}
