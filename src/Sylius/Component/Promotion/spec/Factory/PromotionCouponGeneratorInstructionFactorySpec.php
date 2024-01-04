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

namespace spec\Sylius\Component\Promotion\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstruction;

final class PromotionCouponGeneratorInstructionFactorySpec extends ObjectBehavior
{
    function it_creates_from_array(): void
    {
        $now = new \DateTime();
        $data = [
            'expiresAt' => $now,
            'suffix' => 'suffix',
            'prefix' => 'prefix',
            'codeLength' => 10,
            'amount' => 1,
            'usageLimit' => 7,
        ];
        $this->createFromArray($data)->shouldBeLike(new PromotionCouponGeneratorInstruction(1, 'prefix', 10, 'suffix', $now, 7));
    }
}
