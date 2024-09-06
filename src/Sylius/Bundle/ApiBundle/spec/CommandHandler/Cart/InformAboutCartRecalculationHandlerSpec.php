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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Cart;

use PhpSpec\ObjectBehavior;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Bundle\ApiBundle\Command\Cart\InformAboutCartRecalculation;
use Sylius\Bundle\ApiBundle\Exception\OrderNoLongerEligibleForPromotion;

final class InformAboutCartRecalculationHandlerSpec extends ObjectBehavior
{
    use MessageHandlerAttributeTrait;

    function it_throws_order_no_longer_eligible_for_promotion_exception(): void
    {
        $this
            ->shouldThrow(OrderNoLongerEligibleForPromotion::class)
            ->during('__invoke', [new InformAboutCartRecalculation('Holiday Sale')])
        ;
    }
}
