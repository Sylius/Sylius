<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Filter;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Filter\TaxonFilterInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TaxonFilterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Filter\TaxonFilter');
    }

    function it_implements_taxon_filter_interface()
    {
        $this->shouldImplement(TaxonFilterInterface::class);
    }

    function it_filters_passed_order_items_with_given_configuration(
        Collection $items,
        \Iterator $iterator,
        OrderItemInterface $item1,
        OrderItemInterface $item2,
        ProductInterface $product1,
        ProductInterface $product2,
        TaxonInterface $taxon1,
        TaxonInterface $taxon2
    ) {
        $items->count()->willReturn(1);
        $items->getIterator()->willReturn($iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, true, false)->shouldBeCalled();
        $iterator->current()->willReturn($item1, $item2);
        $iterator->next()->shouldBeCalled();

        $item1->getProduct()->willReturn($product1);
        $product1->getTaxons()->willReturn([$taxon1]);
        $taxon1->getCode()->willReturn('taxon1');

        $item2->getProduct()->willReturn($product2);
        $product2->getTaxons()->willReturn([$taxon2]);
        $taxon2->getCode()->willReturn('taxon2');

        $this->filter($items, ['filters' => ['taxons' => ['taxon1']]])->shouldReturn([$item1]);
    }

    function it_returns_all_items_if_configuration_is_invalid(Collection $items)
    {
        $this->filter($items, [])->shouldReturn($items);
    }
}
