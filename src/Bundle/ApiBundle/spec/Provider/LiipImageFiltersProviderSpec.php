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

namespace spec\Sylius\Bundle\ApiBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Provider\ImageFiltersProviderInterface;

final class LiipImageFiltersProviderSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([
            'sylius_shop_original' => 'args',
            'sylius_admin_original' => 'args',
            'custom_image' => 'args',
        ]);
    }

    function it_is_an_image_filters_provider(): void
    {
        $this->shouldImplement(ImageFiltersProviderInterface::class);
    }

    function it_returns_image_filters(): void
    {
        $this->getFilters()->shouldReturn([
            'sylius_shop_original',
            'sylius_admin_original',
            'custom_image',
        ]);
    }
}
