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

namespace spec\Sylius\Component\Core\Promotion\Filter;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;

final class CompositeFilterSpec extends ObjectBehavior
{
    function let(FilterInterface $filter1, FilterInterface $filter2): void
    {
        $this->beConstructedWith([$filter1, $filter2]);
    }

    function it_implements_filter_interface(): void
    {
        $this->shouldImplement(FilterInterface::class);
    }

    function it_throws_exception_if_filters_are_not_instance_of_filter_interface(PromotionInterface $invalidFilter): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [[$invalidFilter]]);
    }

    function it_throws_exception_if_filters_array_is_empty(): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('__construct', [[]]);
    }

    function it_filters_items_using_all_composite_filters(FilterInterface $filter1, FilterInterface $filter2): void
    {
        $items = ['item1', 'item2', 'item3'];
        $configuration = ['some_configuration'];

        $filter1->filter($items, $configuration)->willReturn(['item1', 'item2']);
        $filter2->filter(['item1', 'item2'], $configuration)->willReturn(['item1']);

        $this->filter($items, $configuration)->shouldReturn(['item1']);
    }
}
