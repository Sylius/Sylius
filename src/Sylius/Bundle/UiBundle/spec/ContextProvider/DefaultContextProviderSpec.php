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

namespace spec\Sylius\Bundle\UiBundle\ContextProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UiBundle\ContextProvider\ContextProviderInterface;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;

final class DefaultContextProviderSpec extends ObjectBehavior
{
    function it_is_a_context_provider_interface(): void
    {
        $this->shouldImplement(ContextProviderInterface::class);
    }

    function it_replaces_block_context_with_a_template_context(): void
    {
        $templateBlock = new TemplateBlock('block_name', 'event_name', 'block.txt.twig', ['foo' => 'quux', 'quuz' => 'corge'], 0, true, null);

        $this
            ->provide(['foo' => 'bar', 'baz' => 'qux'], $templateBlock)
            ->shouldReturn([
                'foo' => 'bar',
                'quuz' => 'corge',
                'baz' => 'qux',
            ])
        ;
    }

    function it_supports_all_template_blocks(): void
    {
        $this
            ->supports(new TemplateBlock('block_name', 'event_name', null, null, null, null, null))
            ->shouldReturn(true)
        ;

        $this
            ->supports(new TemplateBlock('block_name', 'event_name', 'block.txt.twig', ['foo' => 'quux', 'quuz' => 'corge'], 0, true, null))
            ->shouldReturn(true)
        ;
    }
}
