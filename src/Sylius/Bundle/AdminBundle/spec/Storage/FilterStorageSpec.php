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

namespace spec\Sylius\Bundle\AdminBundle\Storage;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AdminBundle\Storage\FilterStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class FilterStorageSpec extends ObjectBehavior
{
    function let(SessionInterface $session): void
    {
        $this->beConstructedWith($session);
    }

    function it_implements_a_cart_storage_interface(): void
    {
        $this->shouldImplement(FilterStorageInterface::class);
    }

    function it_sets_a_filters_in_a_session(SessionInterface $session): void
    {
        $filters = [
            'filter' => 'value',
        ];

        $session->set('filters', $filters)->shouldBeCalled();

        $this->set($filters);
    }

    function it_returns_all_filters_from_a_session(SessionInterface $session): void
    {
        $filters = [
            'filter' => 'value',
        ];

        $session->get('filters', [])->willReturn($filters);

        $this->all()->shouldReturn($filters);
    }

    function it_returns_true_if_filters_are_set(SessionInterface $session): void
    {
        $filters = [
            'filter' => 'value',
        ];

        $session->get('filters', [])->willReturn($filters);

        $this->hasFilters()->shouldReturn(true);
    }

    function it_returns_false_if_filters_are_not_set(SessionInterface $session): void
    {
        $filters = [];

        $session->get('filters', [])->willReturn($filters);

        $this->hasFilters()->shouldReturn(false);
    }
}
