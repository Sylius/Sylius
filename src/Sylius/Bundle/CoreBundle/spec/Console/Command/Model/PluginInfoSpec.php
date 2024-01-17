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

namespace spec\Sylius\Bundle\CoreBundle\Console\Command\Model;

use PhpSpec\ObjectBehavior;

final class PluginInfoSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(
            'Admin Order Creation',
            'Creating (and copying) orders in the administration panel.',
            'https://github.com/Sylius/AdminOrderCreationPlugin',
        );
    }

    function it_has_a_name(): void
    {
        $this->name()->shouldBeLike('Admin Order Creation');
    }

    function it_has_a_description(): void
    {
        $this->description()->shouldBeLike('Creating (and copying) orders in the administration panel.');
    }

    function it_has_a_url(): void
    {
        $this->url()->shouldBeLike('https://github.com/Sylius/AdminOrderCreationPlugin');
    }
}
