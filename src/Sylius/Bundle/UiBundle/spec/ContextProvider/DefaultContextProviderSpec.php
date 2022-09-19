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

namespace spec\Sylius\Bundle\UiBundle\ContextProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UiBundle\ContextProvider\ContextProviderInterface;

final class DefaultContextProviderSpec extends ObjectBehavior
{
    function it_is_a_context_provider_interface(): void
    {
        $this->shouldImplement(ContextProviderInterface::class);
    }

    function it_replaces_block_context_with_a_template_context(): void
    {
        $this
            ->provide(
                [
                    'foo' => 'bar',
                    'baz' => 'qux',
                ],
                [
                    'foo' => 'quux',
                    'quuz' => 'corge',
                ],
            )
            ->shouldReturn([
                'foo' => 'bar',
                'quuz' => 'corge',
                'baz' => 'qux',
            ])
        ;
    }
}
