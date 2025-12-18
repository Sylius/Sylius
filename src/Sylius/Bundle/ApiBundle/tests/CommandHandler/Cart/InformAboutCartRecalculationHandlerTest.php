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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler\Cart;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Cart\InformAboutCartRecalculation;
use Sylius\Bundle\ApiBundle\CommandHandler\Cart\InformAboutCartRecalculationHandler;
use Sylius\Bundle\ApiBundle\Exception\OrderNoLongerEligibleForPromotion;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class InformAboutCartRecalculationHandlerTest extends TestCase
{
    private InformAboutCartRecalculationHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = new InformAboutCartRecalculationHandler();
    }

    use MessageHandlerAttributeTrait;

    public function testThrowsOrderNoLongerEligibleForPromotionException(): void
    {
        self::expectException(OrderNoLongerEligibleForPromotion::class);
        $this->handler->__invoke(new InformAboutCartRecalculation('Holiday Sale'));
    }
}
