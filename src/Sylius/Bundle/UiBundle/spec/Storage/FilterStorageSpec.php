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

namespace spec\Sylius\Bundle\UiBundle\Storage;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UiBundle\Storage\FilterStorageInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class FilterStorageSpec extends ObjectBehavior
{
    function let(RequestStack $requestStack): void
    {
        $this->beConstructedWith($requestStack);
    }

    function it_implements_a_cart_storage_interface(): void
    {
        $this->shouldImplement(FilterStorageInterface::class);
    }

    function it_sets_filters_in_a_session(RequestStack $requestStack, SessionInterface $session): void
    {
        $filters = [
            'filter' => 'value',
        ];

        $requestStack->getSession()->willReturn($session);
        $session->set('filters', $filters)->shouldBeCalled();

        $this->set($filters);
    }

    function it_returns_all_filters_from_a_session(RequestStack $requestStack, SessionInterface $session): void
    {
        $filters = [
            'filter' => 'value',
        ];

        $requestStack->getSession()->willReturn($session);
        $session->get('filters', [])->willReturn($filters);

        $this->all()->shouldReturn($filters);
    }

    function it_returns_true_if_filters_are_set(RequestStack $requestStack, SessionInterface $session): void
    {
        $filters = [
            'filter' => 'value',
        ];

        $requestStack->getSession()->willReturn($session);
        $session->get('filters', [])->willReturn($filters);

        $this->hasFilters()->shouldReturn(true);
    }

    function it_returns_false_if_filters_are_not_set(RequestStack $requestStack, SessionInterface $session): void
    {
        $filters = [];

        $requestStack->getSession()->willReturn($session);
        $session->get('filters', [])->willReturn($filters);

        $this->hasFilters()->shouldReturn(false);
    }
}
