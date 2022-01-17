<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\spec\Applicator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Calculator\ActionBasedPriceCalculatorInterface;
use Sylius\Bundle\CoreBundle\Calculator\CatalogPromotionPriceCalculator;

final class ActionBasedDiscountApplicatorSpec extends ObjectBehavior
{
    function let(CatalogPromotionPriceCalculator $priceCalculator)
    {
        $this->beConstructedWith($priceCalculator);
    }

    function it_implements_action_based_discount_applicator_interface(): void
    {
        $this->shouldImplement(ActionBasedPriceCalculatorInterface::class);
    }

}
