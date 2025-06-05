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
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Bundle\ApiBundle\Command\Cart\InformAboutCartRecalculation;
use Sylius\Bundle\ApiBundle\CommandHandler\Cart\InformAboutCartRecalculationHandler;
use Sylius\Bundle\ApiBundle\Exception\OrderNoLongerEligibleForPromotion;

final class InformAboutCartRecalculationHandlerTest extends TestCase
{
    private InformAboutCartRecalculationHandler $informAboutCartRecalculationHandler;

    protected function setUp(): void
    {
        $this->informAboutCartRecalculationHandler = new InformAboutCartRecalculationHandler();
    }

    use MessageHandlerAttributeTrait;

    public function testThrowsOrderNoLongerEligibleForPromotionException(): void
    {
        $this->expectException(OrderNoLongerEligibleForPromotion::class);
        $this->informAboutCartRecalculationHandler->__invoke(new InformAboutCartRecalculation('Holiday Sale'));
    }
}
