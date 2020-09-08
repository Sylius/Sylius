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

namespace spec\Sylius\Behat\Service\Converter;

use PhpSpec\ObjectBehavior;
use Sylius\Behat\Service\Converter\AdminToShopIriConverterInterface;

final class AdminToShopIriConverterSpec extends ObjectBehavior
{
    function it_is_an_admin_to_shop_iri_converter(): void
    {
        $this->shouldImplement(AdminToShopIriConverterInterface::class);
    }

    function it_converts_admin_iri_to_shop_iri(): void
    {
        $this->convert('new-api/admin/order/products/TEST')->shouldReturn('new-api/shop/order/products/TEST');
    }
}
