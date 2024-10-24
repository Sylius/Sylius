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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Cart;

use Sylius\Bundle\ApiBundle\Command\Cart\InformAboutCartRecalculation;
use Sylius\Bundle\ApiBundle\Exception\OrderNoLongerEligibleForPromotion;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class InformAboutCartRecalculationHandler
{
    public function __invoke(InformAboutCartRecalculation $command): void
    {
        throw new OrderNoLongerEligibleForPromotion($command->promotionName);
    }
}
