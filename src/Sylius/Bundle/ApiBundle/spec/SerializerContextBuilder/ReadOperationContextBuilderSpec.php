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

namespace spec\Sylius\Bundle\ApiBundle\SerializerContextBuilder;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;

final class ReadOperationContextBuilderSpec extends ObjectBehavior
{
    function let(SerializerContextBuilderInterface $decoratedSerializerContextBuilder): void
    {
        $this->beConstructedWith($decoratedSerializerContextBuilder, false, false);
    }

    function it_updates_an_context_with_index_and_show_serialization_groups_if_only_read_provided(
        SerializerContextBuilderInterface $decoratedSerializerContextBuilder,
        Request $request,
    ): void {
        $decoratedSerializerContextBuilder->createFromRequest($request, true, [])->willReturn([
            'groups' => ['foo:read'],
        ]);

        $this->createFromRequest($request, true, [])->shouldReturn([
            'groups' => ['foo:read', 'foo:index', 'foo:show'],
        ]);
    }

    function it_updates_an_context_with_read_serialization_groups_if_only_index_and_show_provided(
        SerializerContextBuilderInterface $decoratedSerializerContextBuilder,
        Request $request,
    ): void {
        $decoratedSerializerContextBuilder->createFromRequest($request, true, [])->willReturn([
            'groups' => ['foo:read'],
        ]);

        $this->createFromRequest($request, true, [])->shouldReturn([
            'groups' => ['foo:read', 'foo:index', 'foo:show'],
        ]);
    }

    function it_does_not_update_context_with_read_group_if_skip_adding_read_parameter_is_set_to_true(
        SerializerContextBuilderInterface $decoratedSerializerContextBuilder,
        Request $request,
    ): void {
        $this->beConstructedWith($decoratedSerializerContextBuilder, true, false);

        $decoratedSerializerContextBuilder->createFromRequest($request, true, [])->willReturn([
            'groups' => ['foo:show'],
        ]);

        $this->createFromRequest($request, true, [])->shouldReturn([
            'groups' => ['foo:show'],
        ]);
    }

    function it_does_not_update_context_with_show_and_index_group_if_skip_adding_show_and_index_is_set_to_true(
        SerializerContextBuilderInterface $decoratedSerializerContextBuilder,
        Request $request,
    ): void {
        $this->beConstructedWith($decoratedSerializerContextBuilder, false, true);

        $decoratedSerializerContextBuilder->createFromRequest($request, true, [])->willReturn([
            'groups' => ['foo:read'],
        ]);

        $this->createFromRequest($request, true, [])->shouldReturn([
            'groups' => ['foo:read'],
        ]);
    }
}
