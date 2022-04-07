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

namespace spec\Sylius\Behat\Service\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Behat\Service\Resolver\PriceResolver;
use Sylius\Behat\Service\Resolver\PriceResolverInterface;

final class PriceResolverSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith();
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(PriceResolver::class);
    }

    function it_implements_current_page_resolver_interface(): void
    {
        $this->shouldImplement(PriceResolverInterface::class);
    }

    function it_gets_correct_price_from_string(): void
    {
        $this->getPriceFromString('£20.4')->shouldReturn(2040);
    }
}
